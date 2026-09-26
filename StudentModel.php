<?php
// Model/StudentModel.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/AuthModel.php';

class StudentModel {
    private $db;
    private $auth;

    public function __construct() {
        $this->db = getDB();
        $this->auth = new AuthModel();
    }

    public function getStudentProfile($user_id) {
        $stmt = $this->db->prepare("SELECT s.*, u.username, d.dept_name, d.dept_code 
                                    FROM students s 
                                    JOIN users u ON s.user_id = u.user_id 
                                    JOIN departments d ON s.dept_id = d.dept_id 
                                    WHERE s.user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }

    public function getEnrolledCourses($student_id) {
        $sql = "SELECT e.*, ca.academic_year, ca.semester, ca.section, ca.marks_status,
                c.course_id, c.course_code, c.course_title, c.credit_hours, c.course_type,
                t.name as teacher_name, t.email as teacher_email, t.phone as teacher_phone, t.office_room as teacher_room,
                wr.status as withdraw_status, wr.request_id as withdraw_request_id,
                fr.grand_total, fr.letter_grade, fr.grade_point, fr.status as result_status, fr.is_published
                FROM enrollments e
                JOIN course_assignments ca ON e.assignment_id = ca.assignment_id
                JOIN courses c ON ca.course_id = c.course_id
                JOIN teachers t ON ca.teacher_id = t.teacher_id
                LEFT JOIN withdraw_requests wr ON e.enrollment_id = wr.enrollment_id
                LEFT JOIN final_results fr ON e.enrollment_id = fr.enrollment_id
                WHERE e.student_id = ?
                ORDER BY ca.academic_year DESC, ca.semester ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$student_id]);
        return $stmt->fetchAll();
    }

    // Semester-wise Result & Detailed Component Breakdown
    public function getSemesterResults($student_id, $semester = null) {
        $sql = "SELECT fr.*, e.enrollment_type, e.academic_year, e.semester,
                c.course_id, c.course_code, c.course_title, c.credit_hours, c.course_type,
                t.name as teacher_name, t.email as teacher_email
                FROM final_results fr
                JOIN enrollments e ON fr.enrollment_id = e.enrollment_id
                JOIN course_assignments ca ON fr.assignment_id = ca.assignment_id
                JOIN courses c ON ca.course_id = c.course_id
                JOIN teachers t ON ca.teacher_id = t.teacher_id
                WHERE fr.student_id = ? AND fr.is_published = 1 AND e.status != 'withdrawn'";
        $params = [$student_id];
        if ($semester) {
            $sql .= " AND e.semester = ?";
            $params[] = $semester;
        }
        $sql .= " ORDER BY e.semester ASC, c.course_code ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Complete 8-Semester Academic Transcript with CGPA
    public function getFullTranscript($student_id) {
        $results = $this->getSemesterResults($student_id);

        $semesters_data = [];
        $total_credits_earned = 0.0;
        $total_credits_attempted = 0.0;
        $total_weighted_points = 0.0;

        // Group by semester (1 to 8)
        foreach ($results as $r) {
            $sem = (int)$r['semester'];
            if (!isset($semesters_data[$sem])) {
                $semesters_data[$sem] = [
                    'semester' => $sem,
                    'courses' => [],
                    'semester_credits' => 0.0,
                    'semester_points' => 0.0,
                    'sgpa' => 0.00
                ];
            }

            $cr = (float)$r['credit_hours'];
            $gp = (float)$r['grade_point'];
            $earned = ($r['letter_grade'] !== 'F');

            $semesters_data[$sem]['courses'][] = $r;
            $semesters_data[$sem]['semester_credits'] += $cr;
            $semesters_data[$sem]['semester_points'] += ($gp * $cr);

            $total_credits_attempted += $cr;
            if ($earned) {
                $total_credits_earned += $cr;
            }
            $total_weighted_points += ($gp * $cr);
        }

        // Calculate SGPA for each semester
        foreach ($semesters_data as $sem => &$sdata) {
            if ($sdata['semester_credits'] > 0) {
                $sdata['sgpa'] = round($sdata['semester_points'] / $sdata['semester_credits'], 2);
            }
        }

        // Cumulative CGPA
        $cgpa = ($total_credits_attempted > 0) ? round($total_weighted_points / $total_credits_attempted, 2) : 0.00;

        return [
            'semesters' => $semesters_data,
            'total_credits_attempted' => $total_credits_attempted,
            'total_credits_earned' => $total_credits_earned,
            'cgpa' => $cgpa,
            'overall_grade_info' => getGradeInfo($cgpa * 25) // approx equivalent percentage for 4.0 scale
        ];
    }

    // Course Withdrawal Request (Submitted by student)
    public function submitWithdrawRequest($enrollment_id, $student_id, $assignment_id, $reason) {
        // Verify enrollment belongs to student
        $stmt = $this->db->prepare("SELECT e.*, c.course_code, c.course_title, ca.teacher_id, t.user_id as teacher_user_id 
                                    FROM enrollments e
                                    JOIN course_assignments ca ON e.assignment_id = ca.assignment_id
                                    JOIN courses c ON ca.course_id = c.course_id
                                    JOIN teachers t ON ca.teacher_id = t.teacher_id
                                    WHERE e.enrollment_id = ? AND e.student_id = ? AND e.status = 'enrolled'");
        $stmt->execute([$enrollment_id, $student_id]);
        $enr = $stmt->fetch();

        if (!$enr) {
            throw new Exception("Active course enrollment not found.");
        }

        $ins = $this->db->prepare("INSERT INTO withdraw_requests (enrollment_id, student_id, assignment_id, reason, status)
            VALUES (?, ?, ?, ?, 'pending')
            ON DUPLICATE KEY UPDATE reason = VALUES(reason), status = 'pending'");
        $res = $ins->execute([$enrollment_id, $student_id, $assignment_id, $reason]);

        if ($res) {
            // Notify teacher
            $this->auth->createNotification($enr['teacher_user_id'], 'New Course Withdrawal Request', "A student requested to withdraw from {$enr['course_code']} - {$enr['course_title']}.", 'warning', '/teacher/withdraw-requests');
        }
        return $res;
    }

    public function getStudentWithdrawRequests($student_id) {
        $sql = "SELECT wr.*, c.course_code, c.course_title, t.name as teacher_name
                FROM withdraw_requests wr
                JOIN course_assignments ca ON wr.assignment_id = ca.assignment_id
                JOIN courses c ON ca.course_id = c.course_id
                JOIN teachers t ON ca.teacher_id = t.teacher_id
                WHERE wr.student_id = ?
                ORDER BY wr.request_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$student_id]);
        return $stmt->fetchAll();
    }

    // Retake Appeals: Eligible courses where published Grade < C (< 2.25 GP or < 45% i.e. D or F)
    public function getEligibleRetakeCourses($student_id) {
        $sql = "SELECT fr.*, c.course_id, c.course_code, c.course_title, c.credit_hours, c.semester,
                ra.appeal_id, ra.status as appeal_status, ra.admin_remarks
                FROM final_results fr
                JOIN enrollments e ON fr.enrollment_id = e.enrollment_id
                JOIN course_assignments ca ON fr.assignment_id = ca.assignment_id
                JOIN courses c ON ca.course_id = c.course_id
                LEFT JOIN retake_appeals ra ON (ra.course_id = c.course_id AND ra.student_id = ? AND ra.status = 'pending')
                WHERE fr.student_id = ? AND fr.is_published = 1 AND fr.grade_point < 2.25
                ORDER BY c.semester ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$student_id, $student_id]);
        return $stmt->fetchAll();
    }

    public function submitRetakeAppeal($student_id, $course_id, $previous_result_id, $reason) {
        // Validate that grade is strictly < C (< 2.25)
        $st = $this->db->prepare("SELECT * FROM final_results WHERE result_id = ? AND student_id = ? AND is_published = 1");
        $st->execute([$previous_result_id, $student_id]);
        $res = $st->fetch();

        if (!$res) {
            throw new Exception("Previous result record not found.");
        }

        if (!isRetakeEligible($res['grade_point'], $res['letter_grade'])) {
            throw new Exception("You can only appeal to retake courses with grades less than 'C' (Grade Point < 2.25). Your current grade is {$res['letter_grade']} ({$res['grade_point']})!");
        }

        // Check if there is already a pending appeal
        $chk = $this->db->prepare("SELECT appeal_id FROM retake_appeals WHERE student_id = ? AND course_id = ? AND status = 'pending'");
        $chk->execute([$student_id, $course_id]);
        if ($chk->fetch()) {
            throw new Exception("You already have a pending retake appeal for this course!");
        }

        $ins = $this->db->prepare("INSERT INTO retake_appeals (student_id, course_id, previous_result_id, previous_grade, previous_grade_point, reason, status)
            VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $done = $ins->execute([$student_id, $course_id, $previous_result_id, $res['letter_grade'], $res['grade_point'], $reason]);

        if ($done) {
            // Notify Admin
            $admin_users = $this->db->query("SELECT user_id FROM users WHERE role = 'admin'")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($admin_users as $aid) {
                $this->auth->createNotification($aid, 'New Course Retake Appeal', "Student submitted a course retake appeal for approval.", 'warning', '/admin/retake-appeals');
            }
        }
        return $done;
    }

    public function getStudentRetakeAppeals($student_id) {
        $sql = "SELECT ra.*, c.course_code, c.course_title, c.credit_hours, c.semester
                FROM retake_appeals ra
                JOIN courses c ON ra.course_id = c.course_id
                WHERE ra.student_id = ?
                ORDER BY ra.appeal_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$student_id]);
        return $stmt->fetchAll();
    }
}