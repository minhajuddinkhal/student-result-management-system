<?php
// Controller/ReportController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/StudentModel.php';
require_once __DIR__ . '/../Model/AdminModel.php';

class ReportController {
    public function printStudentTranscript() {
        requireLogin();
        $student_id = $_GET['student_id'] ?? null;
        
        if ($_SESSION['role'] === 'student') {
            $student_id = $_SESSION['details']['student_id'];
        } elseif (!in_array($_SESSION['role'], ['admin', 'teacher'])) {
            die('Unauthorized');
        }

        $sm = new StudentModel();
        $st_profile_stmt = getDB()->prepare("SELECT s.*, u.username, d.dept_name, d.dept_code FROM students s 
                                             JOIN users u ON s.user_id = u.user_id 
                                             JOIN departments d ON s.dept_id = d.dept_id 
                                             WHERE s.student_id = ?");
        $st_profile_stmt->execute([$student_id]);
        $student_info = $st_profile_stmt->fetch();

        if (!$student_info) {
            die("Student record not found.");
        }

        $transcript = $sm->getFullTranscript($student_id);
        require_once __DIR__ . '/../View/student/marksheet_print.php';
    }
}