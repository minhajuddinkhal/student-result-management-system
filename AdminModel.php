<?php
// Model/AdminModel.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/AuthModel.php';

class AdminModel {
    private $db;
    private $auth;

    public function __construct() {
        $this->db = getDB();
        $this->auth = new AuthModel();
    }

    public function getDashboardStats() {
        $stats = [];
        $stats['total_students'] = $this->db->query("SELECT COUNT(*) FROM students")->fetchColumn();
        $stats['total_teachers'] = $this->db->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
        $stats['total_departments'] = $this->db->query("SELECT COUNT(*) FROM departments")->fetchColumn();
        $stats['total_courses'] = $this->db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
        $stats['pending_marks'] = $this->db->query("SELECT COUNT(*) FROM course_assignments WHERE marks_status = 'submitted'")->fetchColumn();
        $stats['pending_appeals'] = $this->db->query("SELECT COUNT(*) FROM retake_appeals WHERE status = 'pending'")->fetchColumn();
        return $stats;
    }

    // Departments
    public function getDepartments() {
        return $this->db->query("SELECT d.*, 
            (SELECT COUNT(*) FROM teachers t WHERE t.dept_id = d.dept_id) as teacher_count,
            (SELECT COUNT(*) FROM students s WHERE s.dept_id = d.dept_id) as student_count,
            (SELECT COUNT(*) FROM courses c WHERE c.dept_id = d.dept_id) as course_count
            FROM departments d ORDER BY d.dept_name ASC")->fetchAll();
    }

    public function addDepartment($code, $name, $desc = '') {
        $stmt = $this->db->prepare("INSERT INTO departments (dept_code, dept_name, description) VALUES (?, ?, ?)");
        return $stmt->execute([strtoupper($code), $name, $desc]);
    }

    public function updateDepartment($id, $code, $name, $desc = '') {
        $stmt = $this->db->prepare("UPDATE departments SET dept_code = ?, dept_name = ?, description = ? WHERE dept_id = ?");
        return $stmt->execute([strtoupper($code), $name, $desc, $id]);
    }

    public function deleteDepartment($id) {
        $stmt = $this->db->prepare("DELETE FROM departments WHERE dept_id = ?");
        return $stmt->execute([$id]);
    }

    // Teachers
    public function getTeachers() {
        $sql = "SELECT t.*, u.username, u.status as user_status, d.dept_name, d.dept_code,
                (SELECT COUNT(*) FROM course_assignments ca WHERE ca.teacher_id = t.teacher_id) as assigned_courses_count
                FROM teachers t
                JOIN users u ON t.user_id = u.user_id
                JOIN departments d ON t.dept_id = d.dept_id
                ORDER BY t.name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function addTeacher($username, $password, $email, $phone, $name, $teacher_code, $dept_id, $designation, $office_room = '', $bio = '') {
        $clean_phone = validateBdPhone($phone);
        if (!$clean_phone) {
            throw new Exception("Invalid Bangladeshi phone number! Must be 11 digits starting with 013-019.");
        }

        $this->db->beginTransaction();
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, role, phone) VALUES (?, ?, ?, 'teacher', ?)");
            $stmt->execute([$username, $email, $hash, $clean_phone]);
            $user_id = $this->db->lastInsertId();

            $stmt_t = $this->db->prepare("INSERT INTO teachers (user_id, teacher_code, name, email, phone, dept_id, designation, office_room, bio) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_t->execute([$user_id, $teacher_code, $name, $email, $clean_phone, $dept_id, $designation, $office_room, $bio]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateTeacher($teacher_id, $name, $email, $phone, $dept_id, $designation, $office_room, $bio) {
        $clean_phone = validateBdPhone($phone);
        if (!$clean_phone) {
            throw new Exception("Invalid Bangladeshi phone number! Must be 11 digits starting with 013-019.");
        }

        $this->db->beginTransaction();
        try {
            $stmt_t = $this->db->prepare("UPDATE teachers SET name = ?, email = ?, phone = ?, dept_id = ?, designation = ?, office_room = ?, bio = ? WHERE teacher_id = ?");
            $stmt_t->execute([$name, $email, $clean_phone, $dept_id, $designation, $office_room, $bio, $teacher_id]);

            $stmt_u = $this->db->prepare("UPDATE users u JOIN teachers t ON u.user_id = t.user_id SET u.email = ?, u.phone = ? WHERE t.teacher_id = ?");
            $stmt_u->execute([$email, $clean_phone, $teacher_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteTeacher($teacher_id) {
        $stmt = $this->db->prepare("SELECT user_id FROM teachers WHERE teacher_id = ?");
        $stmt->execute([$teacher_id]);
        $uid = $stmt->fetchColumn();
        if ($uid) {
            $del = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
            return $del->execute([$uid]);
        }
        return false;
    }

    // Students
    public function getStudents($dept_id = null, $semester = null) {
        $sql = "SELECT s.*, u.username, u.status as user_status, d.dept_name, d.dept_code,
                (SELECT COUNT(*) FROM enrollments e WHERE e.student_id = s.student_id AND e.status = 'enrolled') as current_courses_count
                FROM students s
                JOIN users u ON s.user_id = u.user_id
                JOIN departments d ON s.dept_id = d.dept_id
                WHERE 1=1";
        $params = [];
        if ($dept_id) {
            $sql .= " AND s.dept_id = ?";
            $params[] = $dept_id;
        }
        if ($semester) {
            $sql .= " AND s.current_semester = ?";
            $params[] = $semester;
        }
        $sql .= " ORDER BY s.roll_no ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function addStudent($username, $password, $email, $phone, $name, $roll_no, $reg_no, $dept_id, $semester, $session) {
        $clean_phone = validateBdPhone($phone);
        if (!$clean_phone) {
            throw new Exception("Invalid Bangladeshi phone number! Must be 11 digits starting with 013-019.");
        }

        $this->db->beginTransaction();
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, role, phone) VALUES (?, ?, ?, 'student', ?)");
            $stmt->execute([$username, $email, $hash, $clean_phone]);
            $user_id = $this->db->lastInsertId();

            $stmt_s = $this->db->prepare("INSERT INTO students (user_id, roll_no, reg_no, name, email, phone, dept_id, current_semester, academic_session) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_s->execute([$user_id, $roll_no, $reg_no, $name, $email, $clean_phone, $dept_id, $semester, $session]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateStudent($student_id, $name, $email, $phone, $dept_id, $semester, $session, $status = 'active') {
        $clean_phone = validateBdPhone($phone);
        if (!$clean_phone) {
            throw new Exception("Invalid Bangladeshi phone number! Must be 11 digits starting with 013-019.");
        }

        $this->db->beginTransaction();
        try {
            $stmt_s = $this->db->prepare("UPDATE students SET name = ?, email = ?, phone = ?, dept_id = ?, current_semester = ?, academic_session = ?, status = ? WHERE student_id = ?");
            $stmt_s->execute([$name, $email, $clean_phone, $dept_id, $semester, $session, $status, $student_id]);

            $stmt_u = $this->db->prepare("UPDATE users u JOIN students s ON u.user_id = s.user_id SET u.email = ?, u.phone = ? WHERE s.student_id = ?");
            $stmt_u->execute([$email, $clean_phone, $student_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteStudent($student_id) {
        $stmt = $this->db->prepare("SELECT user_id FROM students WHERE student_id = ?");
        $stmt->execute([$student_id]);
        $uid = $stmt->fetchColumn();
        if ($uid) {
            $del = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
            return $del->execute([$uid]);
        }
        return false;
    }

    // Courses across 8 Semesters
    public function getCourses($dept_id = null, $semester = null) {
        $sql = "SELECT c.*, d.dept_name, d.dept_code FROM courses c 
                JOIN departments d ON c.dept_id = d.dept_id WHERE 1=1";
        $params = [];
        if ($dept_id) {
            $sql .= " AND c.dept_id = ?";
            $params[] = $dept_id;
        }
        if ($semester) {
            $sql .= " AND c.semester = ?";
            $params[] = $semester;
        }
        $sql .= " ORDER BY c.semester ASC, c.course_code ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function addCourse($code, $title, $credit, $dept_id, $semester, $type = 'theory', $desc = '') {
        $stmt = $this->db->prepare("INSERT INTO courses (course_code, course_title, credit_hours, dept_id, semester, course_type, description)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([strtoupper($code), $title, $credit, $dept_id, $semester, $type, $desc]);
    }

    public function updateCourse($course_id, $code, $title, $credit, $dept_id, $semester, $type, $desc = '') {
        $stmt = $this->db->prepare("UPDATE courses SET course_code = ?, course_title = ?, credit_hours = ?, dept_id = ?, semester = ?, course_type = ?, description = ? WHERE course_id = ?");
        return $stmt->execute([strtoupper($code), $title, $credit, $dept_id, $semester, $type, $desc, $course_id]);
    }

    public function deleteCourse($course_id) {
        $stmt = $this->db->prepare("DELETE FROM courses WHERE course_id = ?");
        return $stmt->execute([$course_id]);
    }

    // Course Assignments & Student Enrollments
    public function getCourseAssignments($academic_year = null, $semester = null) {
        $sql = "SELECT ca.*, c.course_code, c.course_title, c.credit_hours, c.course_type,
                t.name as teacher_name, t.email as teacher_email, d.dept_name, d.dept_code,
                (SELECT COUNT(*) FROM enrollments e WHERE e.assignment_id = ca.assignment_id AND e.status = 'enrolled') as enrolled_count
                FROM course_assignments ca
                JOIN courses c ON ca.course_id = c.course_id
                JOIN teachers t ON ca.teacher_id = t.teacher_id
                JOIN departments d ON c.dept_id = d.dept_id
                WHERE 1=1";
        $params = [];
        if ($academic_year) {
            $sql .= " AND ca.academic_year = ?";
            $params[] = $academic_year;
        }
        if ($semester) {
            $sql .= " AND ca.semester = ?";
            $params[] = $semester;
        }
        $sql .= " ORDER BY ca.academic_year DESC, ca.semester ASC, c.course_code ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function assignCourseToTeacher($course_id, $teacher_id, $academic_year, $semester, $section = 'A') {
        $stmt = $this->db->prepare("INSERT INTO course_assignments (course_id, teacher_id, academic_year, semester, section)
            VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$course_id, $teacher_id, $academic_year, $semester, $section]);
    }

    public function enrollStudentInCourse($student_id, $assignment_id, $academic_year, $semester, $type = 'regular') {
        $stmt = $this->db->prepare("INSERT INTO enrollments (student_id, assignment_id, academic_year, semester, enrollment_type, status)
            VALUES (?, ?, ?, ?, ?, 'enrolled')
            ON DUPLICATE KEY UPDATE status = 'enrolled'");
        return $stmt->execute([$student_id, $assignment_id, $academic_year, $semester, $type]);
    }

    public function enrollBatchStudents($assignment_id, $dept_id, $semester, $academic_year) {
        $stmt = $this->db->prepare("SELECT student_id FROM students WHERE dept_id = ? AND current_semester = ? AND status = 'active'");
        $stmt->execute([$dept_id, $semester]);
        $students = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $enrolled = 0;
        foreach ($students as $sid) {
            try {
                $this->enrollStudentInCourse($sid, $assignment_id, $academic_year, $semester, 'regular');
                $enrolled++;
            } catch (Exception $e) {}
        }
        return $enrolled;
    }

    public function getEnrollmentsForAssignment($assignment_id) {
        $sql = "SELECT e.*, s.roll_no, s.reg_no, s.name as student_name, s.email as student_email, s.phone as student_phone,
                fr.grand_total, fr.letter_grade, fr.grade_point, fr.status as result_status
                FROM enrollments e
                JOIN students s ON e.student_id = s.student_id
                LEFT JOIN final_results fr ON e.enrollment_id = fr.enrollment_id
                WHERE e.assignment_id = ?
                ORDER BY s.roll_no ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$assignment_id]);
        return $stmt->fetchAll();
    }

    // Marks Approval Workflow (Locks marks for teacher upon approval)
    public function getSubmittedAssignments() {
        $sql = "SELECT ca.*, c.course_code, c.course_title, c.credit_hours,
                t.name as teacher_name, t.email as teacher_email, d.dept_name,
                (SELECT COUNT(*) FROM enrollments e WHERE e.assignment_id = ca.assignment_id AND e.status = 'enrolled') as total_students
                FROM course_assignments ca
                JOIN courses c ON ca.course_id = c.course_id
                JOIN teachers t ON ca.teacher_id = t.teacher_id
                JOIN departments d ON c.dept_id = d.dept_id
                WHERE ca.marks_status IN ('submitted', 'approved')
                ORDER BY ca.marks_status ASC, ca.assignment_id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function approveAndPublishMarks($assignment_id, $admin_id) {
        $this->db->beginTransaction();
        try {
            // Update Course Assignment status to approved (Locked from teacher edits)
            $stmt = $this->db->prepare("UPDATE course_assignments SET marks_status = 'approved' WHERE assignment_id = ?");
            $stmt->execute([$assignment_id]);

            // Update all final_results for this assignment to approved & published
            $stmt_r = $this->db->prepare("UPDATE final_results SET status = 'approved', approved_by = ?, approved_at = NOW(), is_published = 1 
                                          WHERE assignment_id = ?");
            $stmt_r->execute([$admin_id, $assignment_id]);

            // Notify enrolled students
            $stmt_s = $this->db->prepare("SELECT s.user_id, c.course_code, c.course_title 
                                          FROM enrollments e 
                                          JOIN students s ON e.student_id = s.student_id
                                          JOIN course_assignments ca ON e.assignment_id = ca.assignment_id
                                          JOIN courses c ON ca.course_id = c.course_id
                                          WHERE e.assignment_id = ?");
            $stmt_s->execute([$assignment_id]);
            $rows = $stmt_s->fetchAll();

            foreach ($rows as $r) {
                $this->auth->createNotification($r['user_id'], 'Results Published!', "Official result for {$r['course_code']} - {$r['course_title']} has been approved and published.", 'success', '/student/results');
            }

            $this->auth->logAudit($admin_id, 'Approved Marks', "Approved and locked marks for Assignment ID: $assignment_id");
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Admin direct override of marks if correction needed after approval
    public function updateApprovedMark($result_id, $grand_total, $remarks, $admin_id) {
        $g_info = getGradeInfo($grand_total);
        $stmt = $this->db->prepare("UPDATE final_results SET grand_total = ?, percentage = ?, grade_point = ?, letter_grade = ?, remarks = ?, approved_by = ?, approved_at = NOW() WHERE result_id = ?");
        $res = $stmt->execute([$grand_total, $grand_total, $g_info['point'], $g_info['grade'], $remarks, $admin_id, $result_id]);
        if ($res) {
            $this->auth->logAudit($admin_id, 'Admin Mark Correction', "Admin updated result ID: $result_id to Total: $grand_total, Grade: {$g_info['grade']}");
        }
        return $res;
    }

    // Retake Appeals Workflow (< C grade)
    public function getRetakeAppeals($status = null) {
        $sql = "SELECT ra.*, s.roll_no, s.name as student_name, s.email as student_email, s.phone as student_phone, s.current_semester,
                c.course_code, c.course_title, c.credit_hours, d.dept_name,
                u.username as reviewer_name
                FROM retake_appeals ra
                JOIN students s ON ra.student_id = s.student_id
                JOIN courses c ON ra.course_id = c.course_id
                JOIN departments d ON c.dept_id = d.dept_id
                LEFT JOIN users u ON ra.reviewed_by = u.user_id
                WHERE 1=1";
        $params = [];
        if ($status) {
            $sql .= " AND ra.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY ra.appeal_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function reviewRetakeAppeal($appeal_id, $status, $admin_remarks, $admin_id) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("UPDATE retake_appeals SET status = ?, admin_remarks = ?, reviewed_by = ?, reviewed_at = NOW() WHERE appeal_id = ?");
            $stmt->execute([$status, $admin_remarks, $admin_id, $appeal_id]);

            // Fetch appeal info
            $st = $this->db->prepare("SELECT ra.*, s.user_id, s.academic_session, s.current_semester, c.course_code, c.course_title, c.course_id 
                                      FROM retake_appeals ra
                                      JOIN students s ON ra.student_id = s.student_id
                                      JOIN courses c ON ra.course_id = c.course_id
                                      WHERE ra.appeal_id = ?");
            $st->execute([$appeal_id]);
            $appeal = $st->fetch();

            if ($appeal) {
                if ($status === 'approved') {
                    // Automatically assign/enroll course into student's active semester for retake
                    // Find active course assignment for this course
                    $sc = $this->db->prepare("SELECT assignment_id FROM course_assignments WHERE course_id = ? ORDER BY assignment_id DESC LIMIT 1");
                    $sc->execute([$appeal['course_id']]);
                    $asgn_id = $sc->fetchColumn();

                    if ($asgn_id) {
                        $this->enrollStudentInCourse($appeal['student_id'], $asgn_id, $appeal['academic_session'], $appeal['current_semester'], 'retake');
                    }

                    $this->auth->createNotification($appeal['user_id'], 'Retake Appeal Approved!', "Your appeal to retake {$appeal['course_code']} - {$appeal['course_title']} was approved and added to your dashboard.", 'success', '/student/courses');
                } else {
                    $this->auth->createNotification($appeal['user_id'], 'Retake Appeal Rejected', "Your appeal for {$appeal['course_code']} was not approved. Remarks: $admin_remarks", 'danger', '/student/retake-appeal');
                }
            }

            $this->auth->logAudit($admin_id, 'Reviewed Retake Appeal', "Retake appeal #$appeal_id set to $status");
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // User Password Reset List
    public function getAllUsersForReset() {
        return $this->db->query("SELECT u.user_id, u.username, u.email, u.role, u.phone, u.status, u.created_at,
            CASE 
                WHEN u.role = 'teacher' THEN (SELECT name FROM teachers t WHERE t.user_id = u.user_id)
                WHEN u.role = 'student' THEN (SELECT name FROM students s WHERE s.user_id = u.user_id)
                ELSE 'Administrator'
            END as display_name,
            CASE 
                WHEN u.role = 'student' THEN (SELECT roll_no FROM students s WHERE s.user_id = u.user_id)
                WHEN u.role = 'teacher' THEN (SELECT teacher_code FROM teachers t WHERE t.user_id = u.user_id)
                ELSE 'ADMIN'
            END as identifier
            FROM users u ORDER BY u.role ASC, u.username ASC")->fetchAll();
    }

    // Announcements
    public function addAnnouncement($title, $content, $target_role, $admin_id) {
        $stmt = $this->db->prepare("INSERT INTO announcements (title, content, target_role, created_by) VALUES (?, ?, ?, ?)");
        $res = $stmt->execute([$title, $content, $target_role, $admin_id]);
        if ($res) {
            $this->auth->logAudit($admin_id, 'Created Announcement', "Announcement: $title ($target_role)");
        }
        return $res;
    }

    public function deleteAnnouncement($id) {
        $stmt = $this->db->prepare("DELETE FROM announcements WHERE announcement_id = ?");
        return $stmt->execute([$id]);
    }
}