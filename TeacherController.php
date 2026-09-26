<?php
// Controller/TeacherController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/TeacherModel.php';
require_once __DIR__ . '/../Model/AuthModel.php';

class TeacherController {
    private $teacher;
    private $auth;
    private $teacher_info;

    public function __construct() {
        requireRole('teacher');
        $this->teacher = new TeacherModel();
        $this->auth = new AuthModel();
        $this->teacher_info = $this->teacher->getTeacherProfile($_SESSION['user_id']);
    }

    public function dashboard() {
        $teacher_id = $this->teacher_info['teacher_id'];
        $assigned_courses = $this->teacher->getAssignedCourses($teacher_id);
        $withdraw_requests = $this->teacher->getWithdrawRequests($teacher_id);
        $announcements = $this->auth->getAnnouncements('teacher');
        $flash = getFlash();
        $teacher_info = $this->teacher_info;
        require_once __DIR__ . '/../View/teacher/dashboard.php';
    }

    public function courses() {
        $teacher_id = $this->teacher_info['teacher_id'];
        $assigned_courses = $this->teacher->getAssignedCourses($teacher_id);
        $flash = getFlash();
        $teacher_info = $this->teacher_info;
        require_once __DIR__ . '/../View/teacher/courses.php';
    }

    public function assessmentSetup() {
        $assignment_id = $_GET['assignment_id'] ?? null;
        if (!$assignment_id) {
            flash('warning', 'Please select a course to configure assessments.');
            header('Location: ' . BASE_URL . '/teacher/courses');
            exit;
        }

        $assignment = $this->teacher->getAssignmentDetails($assignment_id, $this->teacher_info['teacher_id']);
        if (!$assignment) {
            flash('danger', 'Assignment not found or unauthorized.');
            header('Location: ' . BASE_URL . '/teacher/courses');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $mid_weight = (float)$_POST['mid_weight'];
                $final_weight = (float)$_POST['final_weight'];
                
                $components = [];
                if (isset($_POST['comp_name']) && is_array($_POST['comp_name'])) {
                    foreach ($_POST['comp_name'] as $idx => $name) {
                        if (!empty(trim($name))) {
                            $components[] = [
                                'term_type' => $_POST['comp_term'][$idx] ?? 'mid',
                                'name' => sanitize($name),
                                'max_marks' => (float)$_POST['comp_max'][$idx],
                                'weightage' => (float)$_POST['comp_weight'][$idx]
                            ];
                        }
                    }
                }

                $this->teacher->saveAssessmentSetup($assignment_id, $mid_weight, $final_weight, $components);
                flash('success', 'Assessment marks distribution saved successfully! You can now proceed to Marks Entry.');
                header('Location: ' . BASE_URL . '/teacher/marks-entry?assignment_id=' . $assignment_id);
                exit;
            } catch (Exception $e) {
                flash('danger', 'Error: ' . $e->getMessage());
            }
        }

        $components = $this->teacher->getAssessmentComponents($assignment_id);
        $flash = getFlash();
        $teacher_info = $this->teacher_info;
        require_once __DIR__ . '/../View/teacher/assessment_setup.php';
    }

    public function marksEntry() {
        $assignment_id = $_GET['assignment_id'] ?? null;
        if (!$assignment_id) {
            flash('warning', 'Please select a course for marks entry.');
            header('Location: ' . BASE_URL . '/teacher/courses');
            exit;
        }

        $assignment = $this->teacher->getAssignmentDetails($assignment_id, $this->teacher_info['teacher_id']);
        if (!$assignment) {
            flash('danger', 'Assignment not found or unauthorized.');
            header('Location: ' . BASE_URL . '/teacher/courses');
            exit;
        }

        if (!$assignment['is_configured']) {
            flash('warning', 'Please configure the assessment components first before entering marks.');
            header('Location: ' . BASE_URL . '/teacher/assessment-setup?assignment_id=' . $assignment_id);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $is_submit = isset($_POST['submit_approval']);
            $marks_input = $_POST['marks'] ?? [];

            try {
                $this->teacher->saveMarks($assignment_id, $marks_input, $is_submit);
                if ($is_submit) {
                    flash('success', 'Marks successfully submitted for Administrator approval! Records are now locked until review.');
                } else {
                    flash('success', 'Marks draft saved successfully.');
                }
                header('Location: ' . BASE_URL . '/teacher/marks-entry?assignment_id=' . $assignment_id);
                exit;
            } catch (Exception $e) {
                flash('danger', 'Error: ' . $e->getMessage());
            }
        }

        $grid = $this->teacher->getMarksGrid($assignment_id);
        $flash = getFlash();
        $teacher_info = $this->teacher_info;
        require_once __DIR__ . '/../View/teacher/marks_entry.php';
    }

    public function withdrawRequests() {
        $teacher_id = $this->teacher_info['teacher_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request_id = (int)$_POST['request_id'];
            $status = $_POST['status']; // 'approved' or 'rejected'
            $remarks = sanitize($_POST['teacher_remarks'] ?? '');

            try {
                $this->teacher->reviewWithdrawRequest($request_id, $status, $remarks, $teacher_id);
                flash('success', "Withdrawal request #$request_id has been " . ucfirst($status) . ".");
            } catch (Exception $e) {
                flash('danger', 'Error: ' . $e->getMessage());
            }
            header('Location: ' . BASE_URL . '/teacher/withdraw-requests');
            exit;
        }

        $requests = $this->teacher->getWithdrawRequests($teacher_id);
        $flash = getFlash();
        $teacher_info = $this->teacher_info;
        require_once __DIR__ . '/../View/teacher/withdraw_requests.php';
    }

    public function profile() {
        $teacher_id = $this->teacher_info['teacher_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->teacher->updateTeacherProfile(
                    $teacher_id,
                    $_POST['email'],
                    $_POST['phone'],
                    $_POST['office_room'] ?? '',
                    $_POST['bio'] ?? ''
                );
                flash('success', 'Contact info & profile updated successfully! Students will see this on their dashboard.');
                header('Location: ' . BASE_URL . '/teacher/profile');
                exit;
            } catch (Exception $e) {
                flash('danger', $e->getMessage());
            }
        }

        $teacher_info = $this->teacher->getTeacherProfile($_SESSION['user_id']);
        $flash = getFlash();
        require_once __DIR__ . '/../View/teacher/profile.php';
    }
}