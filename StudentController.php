<?php
// Controller/StudentController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/StudentModel.php';
require_once __DIR__ . '/../Model/AuthModel.php';

class StudentController {
    private $student;
    private $auth;
    private $student_info;

    public function __construct() {
        requireRole('student');
        $this->student = new StudentModel();
        $this->auth = new AuthModel();
        $this->student_info = $this->student->getStudentProfile($_SESSION['user_id']);
    }

    public function dashboard() {
        $student_id = $this->student_info['student_id'];
        $enrolled_courses = $this->student->getEnrolledCourses($student_id);
        $transcript = $this->student->getFullTranscript($student_id);
        $announcements = $this->auth->getAnnouncements('student');
        $flash = getFlash();
        $student_info = $this->student_info;
        require_once __DIR__ . '/../View/student/dashboard.php';
    }

    public function courses() {
        $student_id = $this->student_info['student_id'];
        $enrolled_courses = $this->student->getEnrolledCourses($student_id);
        $flash = getFlash();
        $student_info = $this->student_info;
        require_once __DIR__ . '/../View/student/courses.php';
    }

    public function withdrawRequest() {
        $student_id = $this->student_info['student_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enrollment_id = (int)$_POST['enrollment_id'];
            $assignment_id = (int)$_POST['assignment_id'];
            $reason = sanitize($_POST['reason'] ?? '');

            try {
                $this->student->submitWithdrawRequest($enrollment_id, $student_id, $assignment_id, $reason);
                flash('success', 'Course withdrawal request submitted to course instructor for approval.');
            } catch (Exception $e) {
                flash('danger', 'Error: ' . $e->getMessage());
            }
            header('Location: ' . BASE_URL . '/student/courses');
            exit;
        }

        $requests = $this->student->getStudentWithdrawRequests($student_id);
        $flash = getFlash();
        $student_info = $this->student_info;
        require_once __DIR__ . '/../View/student/withdraw_request.php';
    }

    public function results() {
        $student_id = $this->student_info['student_id'];
        $semester = $_GET['semester'] ?? $this->student_info['current_semester'];
        $results = $this->student->getSemesterResults($student_id, $semester);
        $transcript = $this->student->getFullTranscript($student_id);
        $flash = getFlash();
        $student_info = $this->student_info;
        require_once __DIR__ . '/../View/student/results.php';
    }

    public function transcript() {
        $student_id = $this->student_info['student_id'];
        $transcript = $this->student->getFullTranscript($student_id);
        $flash = getFlash();
        $student_info = $this->student_info;
        require_once __DIR__ . '/../View/student/transcript.php';
    }

    public function retakeAppeal() {
        $student_id = $this->student_info['student_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_id = (int)$_POST['course_id'];
            $result_id = (int)$_POST['previous_result_id'];
            $reason = sanitize($_POST['reason'] ?? '');

            try {
                $this->student->submitRetakeAppeal($student_id, $course_id, $result_id, $reason);
                flash('success', 'Retake appeal submitted to Administrator! Upon approval, course will be assigned to your active semester.');
            } catch (Exception $e) {
                flash('danger', $e->getMessage());
            }
            header('Location: ' . BASE_URL . '/student/retake-appeal');
            exit;
        }

        $eligible_courses = $this->student->getEligibleRetakeCourses($student_id);
        $appeals = $this->student->getStudentRetakeAppeals($student_id);
        $flash = getFlash();
        $student_info = $this->student_info;
        require_once __DIR__ . '/../View/student/retake_appeal.php';
    }

    public function printMarksheet() {
        $student_id = $this->student_info['student_id'];
        $semester = $_GET['semester'] ?? null;
        $results = $this->student->getSemesterResults($student_id, $semester);
        $transcript = $this->student->getFullTranscript($student_id);
        $student_info = $this->student_info;
        require_once __DIR__ . '/../View/student/marksheet_print.php';
    }
}