<?php
// Model/TeacherModel.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/AuthModel.php';

class TeacherModel {
    private $db;
    private $auth;

    public function __construct() {
        $this->db = getDB();
        $this->auth = new AuthModel();
    }

    public function getTeacherProfile($user_id) {
        $stmt = $this->db->prepare("SELECT t.*, u.username, d.dept_name, d.dept_code 
                                    FROM teachers t 
                                    JOIN users u ON t.user_id = u.user_id 
                                    JOIN departments d ON t.dept_id = d.dept_id 
                                    WHERE t.user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }

    public function updateTeacherProfile($teacher_id, $email, $phone, $office_room, $bio) {
        $clean_phone = validateBdPhone($phone);
        if (!$clean_phone) {
            throw new Exception("Invalid Bangladeshi phone number! Must be 11 digits starting with 013-019.");
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("UPDATE teachers SET email = ?, phone = ?, office_room = ?, bio = ? WHERE teacher_id = ?");
            $stmt->execute([$email, $clean_phone, $office_room, $bio, $teacher_id]);

            $stmt_u = $this->db->prepare("UPDATE users u JOIN teachers t ON u.user_id = t.user_id SET u.email = ?, u.phone = ? WHERE t.teacher_id = ?");
            $stmt_u->execute([$email, $clean_phone, $teacher_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getAssignedCourses($teacher_id) {
        $sql = "SELECT ca.*, c.course_code, c.course_title, c.credit_hours, c.course_type, d.dept_name, d.dept_code,
                (SELECT COUNT(*) FROM enrollments e WHERE e.assignment_id = ca.assignment_id AND e.status = 'enrolled') as student_count
                FROM course_assignments ca
                JOIN courses c ON ca.course_id = c.course_id
                JOIN departments d ON c.dept_id = d.dept_id
                WHERE ca.teacher_id = ?
                ORDER BY ca.academic_year DESC, ca.semester ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacher_id]);
        return $stmt->fetchAll();
    }

    public function getAssignmentDetails($assignment_id, $teacher_id = null) {
        $sql = "SELECT ca.*, c.course_code, c.course_title, c.credit_hours, c.course_type, d.dept_name, d.dept_code,
                t.name as teacher_name, t.email as teacher_email, t.phone as teacher_phone
                FROM course_assignments ca
                JOIN courses c ON ca.course_id = c.course_id
                JOIN teachers t ON ca.teacher_id = t.teacher_id
                JOIN departments d ON c.dept_id = d.dept_id
                WHERE ca.assignment_id = ?";
        $params = [$assignment_id];
        if ($teacher_id) {
            $sql .= " AND ca.teacher_id = ?";
            $params[] = $teacher_id;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    // Assessment Setup (Mid % / Final % + Sub-components like Quiz, Attendance, Viva, Exam)
    public function getAssessmentComponents($assignment_id) {
        $stmt = $this->db->prepare("SELECT * FROM assessment_components WHERE assignment_id = ? ORDER BY term_type ASC, component_id ASC");
        $stmt->execute([$assignment_id]);
        return $stmt->fetchAll();
    }

    public function saveAssessmentSetup($assignment_id, $mid_weight, $final_weight, $components) {
        // Total weightage must be 100%
        if (round($mid_weight + $final_weight, 2) != 100.00) {
            throw new Exception("The sum of Midterm weight ($mid_weight%) and Final Term weight ($final_weight%) must be exactly 100%!");
        }

        $this->db->beginTransaction();
        try {
            // Update assignment weights
            $stmt = $this->db->prepare("UPDATE course_assignments SET mid_weight = ?, final_weight = ?, is_configured = 1 WHERE assignment_id = ?");
            $stmt->execute([$mid_weight, $final_weight, $assignment_id]);

            // Clear old components and re-insert
            $del = $this->db->prepare("DELETE FROM assessment_components WHERE assignment_id = ?");
            $del->execute([$assignment_id]);

            $stmt_c = $this->db->prepare("INSERT INTO assessment_components (assignment_id, term_type, component_name, max_marks, weightage) VALUES (?, ?, ?, ?, ?)");
            foreach ($components as $c) {
                if (!empty($c['name'])) {
                    $stmt_c->execute([
                        $assignment_id,
                        $c['term_type'], // 'mid' or 'final'
                        $c['name'],
                        (float)$c['max_marks'],
                        (float)$c['weightage']
                    ]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Marks Grid and Calculations
    public function getMarksGrid($assignment_id) {
        // Fetch components
        $components = $this->getAssessmentComponents($assignment_id);

        // Fetch enrolled students
        $stmt_s = $this->db->prepare("SELECT e.enrollment_id, e.student_id, e.enrollment_type, s.roll_no, s.reg_no, s.name, s.email,
                                      fr.mid_total, fr.final_total, fr.grand_total, fr.letter_grade, fr.grade_point, fr.status as result_status
                                      FROM enrollments e
                                      JOIN students s ON e.student_id = s.student_id
                                      LEFT JOIN final_results fr ON e.enrollment_id = fr.enrollment_id
                                      WHERE e.assignment_id = ? AND e.status = 'enrolled'
                                      ORDER BY s.roll_no ASC");
        $stmt_s->execute([$assignment_id]);
        $students = $stmt_s->fetchAll();

        // Fetch existing marks
        $stmt_m = $this->db->prepare("SELECT m.* FROM marks m 
                                      JOIN enrollments e ON m.enrollment_id = e.enrollment_id 
                                      WHERE e.assignment_id = ?");
        $stmt_m->execute([$assignment_id]);
        $all_marks = $stmt_m->fetchAll();

        $marks_map = []; // [enrollment_id][component_id] = obtained_marks
        foreach ($all_marks as $m) {
            $marks_map[$m['enrollment_id']][$m['component_id']] = (float)$m['obtained_marks'];
        }

        return [
            'components' => $components,
            'students' => $students,
            'marks_map' => $marks_map
        ];
    }

    public function saveMarks($assignment_id, $marks_data, $submit_for_approval = false) {
        $asgn = $this->getAssignmentDetails($assignment_id);
        if (!$asgn) {
            throw new Exception("Assignment not found.");
        }
        if ($asgn['marks_status'] === 'approved') {
            throw new Exception("Marks are already approved by Administrator and locked from editing!");
        }

        $components = $this->getAssessmentComponents($assignment_id);
        $comp_map = [];
        $mid_comp_weight_sum = 0;
        $final_comp_weight_sum = 0;

        foreach ($components as $c) {
            $comp_map[$c['component_id']] = $c;
            if ($c['term_type'] === 'mid') {
                $mid_comp_weight_sum += (float)$c['weightage'];
            } else {
                $final_comp_weight_sum += (float)$c['weightage'];
            }
        }

        $this->db->beginTransaction();
        try {
            $stmt_save_mark = $this->db->prepare("INSERT INTO marks (enrollment_id, component_id, student_id, obtained_marks) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE obtained_marks = VALUES(obtained_marks)");

            $stmt_result = $this->db->prepare("INSERT INTO final_results 
                (enrollment_id, student_id, assignment_id, mid_total, final_total, grand_total, percentage, grade_point, letter_grade, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    mid_total = VALUES(mid_total),
                    final_total = VALUES(final_total),
                    grand_total = VALUES(grand_total),
                    percentage = VALUES(percentage),
                    grade_point = VALUES(grade_point),
                    letter_grade = VALUES(letter_grade),
                    status = VALUES(status)");

            foreach ($marks_data as $enrollment_id => $student_components) {
                // Fetch student_id
                $st_id_stmt = $this->db->prepare("SELECT student_id FROM enrollments WHERE enrollment_id = ?");
                $st_id_stmt->execute([$enrollment_id]);
                $student_id = $st_id_stmt->fetchColumn();
                if (!$student_id) continue;

                $mid_obtained_weighted = 0.0;
                $final_obtained_weighted = 0.0;

                foreach ($student_components as $comp_id => $val) {
                    $obtained = (float)$val;
                    if (!isset($comp_map[$comp_id])) continue;
                    $comp = $comp_map[$comp_id];

                    if ($obtained > (float)$comp['max_marks']) {
                        throw new Exception("Obtained mark ($obtained) for {$comp['component_name']} exceeds maximum allowed marks ({$comp['max_marks']})!");
                    }
                    if ($obtained < 0) {
                        throw new Exception("Marks cannot be negative!");
                    }

                    $stmt_save_mark->execute([$enrollment_id, $comp_id, $student_id, $obtained]);

                    // Calculate weighted contribution
                    if ($comp['max_marks'] > 0) {
                        $contribution = ($obtained / (float)$comp['max_marks']) * (float)$comp['weightage'];
                        if ($comp['term_type'] === 'mid') {
                            $mid_obtained_weighted += $contribution;
                        } else {
                            $final_obtained_weighted += $contribution;
                        }
                    }
                }

                // Scale to configured Mid & Final weights
                // e.g. if mid components weighted sum is 40 and mid_weight is 40
                $mid_score = ($mid_comp_weight_sum > 0) ? ($mid_obtained_weighted / $mid_comp_weight_sum) * (float)$asgn['mid_weight'] : 0;
                $final_score = ($final_comp_weight_sum > 0) ? ($final_obtained_weighted / $final_comp_weight_sum) * (float)$asgn['final_weight'] : 0;
                $grand_total = round($mid_score + $final_score, 2);

                $g_info = getGradeInfo($grand_total);
                $res_status = $submit_for_approval ? 'submitted' : 'draft';

                $stmt_result->execute([
                    $enrollment_id,
                    $student_id,
                    $assignment_id,
                    round($mid_score, 2),
                    round($final_score, 2),
                    $grand_total,
                    $grand_total,
                    $g_info['point'],
                    $g_info['grade'],
                    $res_status
                ]);
            }

            // Update assignment status
            $new_asgn_status = $submit_for_approval ? 'submitted' : 'draft';
            $up = $this->db->prepare("UPDATE course_assignments SET marks_status = ? WHERE assignment_id = ?");
            $up->execute([$new_asgn_status, $assignment_id]);

            if ($submit_for_approval) {
                // Notify admin
                $admin_users = $this->db->query("SELECT user_id FROM users WHERE role = 'admin'")->fetchAll(PDO::FETCH_COLUMN);
                foreach ($admin_users as $aid) {
                    $this->auth->createNotification($aid, 'New Marks Submission for Approval', "Teacher submitted final marks for {$asgn['course_code']} - {$asgn['course_title']}.", 'info', '/admin/marks-approval');
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Course Withdrawal Requests (Teacher reviews and approves/rejects)
    public function getWithdrawRequests($teacher_id) {
        $sql = "SELECT wr.*, s.roll_no, s.reg_no, s.name as student_name, s.email as student_email, s.phone as student_phone,
                c.course_code, c.course_title, ca.section, ca.academic_year, ca.semester
                FROM withdraw_requests wr
                JOIN enrollments e ON wr.enrollment_id = e.enrollment_id
                JOIN course_assignments ca ON wr.assignment_id = ca.assignment_id
                JOIN courses c ON ca.course_id = c.course_id
                JOIN students s ON wr.student_id = s.student_id
                WHERE ca.teacher_id = ?
                ORDER BY wr.request_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacher_id]);
        return $stmt->fetchAll();
    }

    public function reviewWithdrawRequest($request_id, $status, $teacher_remarks, $teacher_id) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("UPDATE withdraw_requests SET status = ?, teacher_remarks = ?, reviewed_by = ?, reviewed_at = NOW() WHERE request_id = ?");
            $stmt->execute([$status, $teacher_remarks, $teacher_id, $request_id]);

            // Fetch request details
            $st = $this->db->prepare("SELECT wr.*, s.user_id, c.course_code, c.course_title 
                                      FROM withdraw_requests wr
                                      JOIN students s ON wr.student_id = s.student_id
                                      JOIN course_assignments ca ON wr.assignment_id = ca.assignment_id
                                      JOIN courses c ON ca.course_id = c.course_id
                                      WHERE wr.request_id = ?");
            $st->execute([$request_id]);
            $req = $st->fetch();

            if ($req) {
                if ($status === 'approved') {
                    // Update enrollment status to withdrawn
                    $up_e = $this->db->prepare("UPDATE enrollments SET status = 'withdrawn' WHERE enrollment_id = ?");
                    $up_e->execute([$req['enrollment_id']]);

                    $this->auth->createNotification($req['user_id'], 'Course Withdrawal Approved', "Your request to withdraw from {$req['course_code']} - {$req['course_title']} has been approved by the course instructor.", 'warning', '/student/courses');
                } else {
                    $this->auth->createNotification($req['user_id'], 'Course Withdrawal Rejected', "Your request to withdraw from {$req['course_code']} was rejected. Teacher Remarks: $teacher_remarks", 'danger', '/student/courses');
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}