<?php
// Controller/AdminController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/AdminModel.php';
require_once __DIR__ . '/../Model/AuthModel.php';
require_once __DIR__ . '/../Model/ResultModel.php';

class AdminController {
    private $admin;
    private $auth;
    private $result;

    public function __construct() {
        requireRole('admin');
        $this->admin = new AdminModel();
        $this->auth = new AuthModel();
        $this->result = new ResultModel();
    }

    public function dashboard() {
        $stats = $this->admin->getDashboardStats();
        $recent_submissions = $this->admin->getSubmittedAssignments();
        $recent_appeals = $this->admin->getRetakeAppeals('pending');
        $announcements = $this->auth->getAnnouncements('admin');
        $flash = getFlash();
        require_once __DIR__ . '/../View/admin/dashboard.php';
    }

    public function departments() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            try {
                if ($action === 'add') {
                    $this->admin->addDepartment($_POST['dept_code'], $_POST['dept_name'], $_POST['description'] ?? '');
                    flash('success', 'Department added successfully.');
                } elseif ($action === 'edit') {
                    $this->admin->updateDepartment($_POST['dept_id'], $_POST['dept_code'], $_POST['dept_name'], $_POST['description'] ?? '');
                    flash('success', 'Department updated successfully.');
                } elseif ($action === 'delete') {
                    $this->admin->deleteDepartment($_POST['dept_id']);
                    flash('success', 'Department deleted successfully.');
                }
            } catch (Exception $e) {
                flash('danger', 'Error: ' . $e->getMessage());
            }
            header('Location: ' . BASE_URL . '/admin/departments');
            exit;
        }

        $departments = $this->admin->getDepartments();
        $flash = getFlash();
        require_once __DIR__ . '/../View/admin/departments.php';
    }

    public function teachers() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            try {
                if ($action === 'add') {
                    $this->admin->addTeacher(
                        $_POST['username'],
                        $_POST['password'],
                        $_POST['email'],
                        $_POST['phone'],
                        $_POST['name'],
                        $_POST['teacher_code'],
                        $_POST['dept_id'],
                        $_POST['designation'],
                        $_POST['office_room'] ?? '',
                        $_POST['bio'] ?? ''
                    );
                    flash('success', 'Teacher registered successfully.');
                } elseif ($action === 'edit') {
                    $this->admin->updateTeacher(
                        $_POST['teacher_id'],
                        $_POST['name'],
                        $_POST['email'],
                        $_POST['phone'],
                        $_POST['dept_id'],
                        $_POST['designation'],
                        $_POST['office_room'] ?? '',
                        $_POST['bio'] ?? ''
                    );
                    flash('success', 'Teacher updated successfully.');
                } elseif ($action === 'delete') {
                    $this->admin->deleteTeacher($_POST['teacher_id']);
                    flash('success', 'Teacher deleted successfully.');
                }
            } catch (Exception $e) {
                flash('danger', $e->getMessage());
            }
            header('Location: ' . BASE_URL . '/admin/teachers');
            exit;
        }

        $teachers = $this->admin->getTeachers();
        $departments = $this->admin->getDepartments();
        $flash = getFlash();
        require_once __DIR__ . '/../View/admin/teachers.php';
    }

    public function students() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            try {
                if ($action === 'add') {
                    $this->admin->addStudent(
                        $_POST['username'],
                        $_POST['password'],
                        $_POST['email'],
                        $_POST['phone'],
                        $_POST['name'],
                        $_POST['roll_no'],
                        $_POST['reg_no'],
                        $_POST['dept_id'],
                        $_POST['semester'],
                        $_POST['session']
                    );
                    flash('success', 'Student registered successfully.');
                } elseif ($action === 'edit') {
                    $this->admin->updateStudent(
                        $_POST['student_id'],
                        $_POST['name'],
                        $_POST['email'],
                        $_POST['phone'],
                        $_POST['dept_id'],
                        $_POST['semester'],
                        $_POST['session'],
                        $_POST['status'] ?? 'active'
                    );
                    flash('success', 'Student updated successfully.');
                } elseif ($action === 'delete') {
                    $this->admin->deleteStudent($_POST['student_id']);
                    flash('success', 'Student record deleted.');
                }
            } catch (Exception $e) {
                flash('danger', $e->getMessage());
            }
            header('Location: ' . BASE_URL . '/admin/students');
            exit;
        }

        $filter_dept = $_GET['dept_id'] ?? null;
        $filter_sem = $_GET['semester'] ?? null;
        $students = $this->admin->getStudents($filter_dept, $filter_sem);
        $departments = $this->admin->getDepartments();
        $flash = getFlash();
        require_once __DIR__ . '/../View/admin/students.php';
    }

    public function courses() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            try {
                if ($action === 'add') {
                    $this->admin->addCourse(
                        $_POST['course_code'],
                        $_POST['course_title'],
                        $_POST['credit_hours'],
                        $_POST['dept_id'],
                        $_POST['semester'],
                        $_POST['course_type'] ?? 'theory',
                        $_POST['description'] ?? ''
                    );
                    flash('success', 'Course added to catalog successfully.');
                } elseif ($action === 'edit') {
                    $this->admin->updateCourse(
                        $_POST['course_id'],
                        $_POST['course_code'],
                        $_POST['course_title'],
                        $_POST['credit_hours'],
                        $_POST['dept_id'],
                        $_POST['semester'],
                        $_POST['course_type'],
                        $_POST['description'] ?? ''
                    );
                    flash('success', 'Course updated successfully.');
                } elseif ($action === 'delete') {
                    $this->admin->deleteCourse($_POST['course_id']);
                    flash('success', 'Course deleted.');
                }
            } catch (Exception $e) {
                flash('danger', 'Error: ' . $e->getMessage());
            }
            header('Location: ' . BASE_URL . '/admin/courses');
            exit;
        }

        $courses = $this->admin->getCourses();
        $departments = $this->admin->getDepartments();
        $flash = getFlash();
        require_once __DIR__ . '/../View/admin/courses.php';
    }

    public function assignCourse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            try {
                if ($action === 'assign_teacher') {
                    $this->admin->assignCourseToTeacher(
                        $_POST['course_id'],
                        $_POST['teacher_id'],
                        $_POST['academic_year'],
                        $_POST['semester'],
                        $_POST['section'] ?? 'A'
                    );
                    flash('success', 'Teacher assigned to course offering successfully.');
                } elseif ($action === 'enroll_batch') {
                    $count = $this->admin->enrollBatchStudents(
                        $_POST['assignment_id'],
                        $_POST['dept_id'],
                        $_POST['semester'],
                        $_POST['academic_year']
                    );
                    flash('success', "Enrolled $count student(s) into the course offering.");
                } elseif ($action === 'enroll_single') {
                    $this->admin->enrollStudentInCourse(
                        $_POST['student_id'],
                        $_POST['assignment_id'],
                        $_POST['academic_year'],
                        $_POST['semester']
                    );
                    flash('success', 'Student enrolled successfully.');
                }
            } catch (Exception $e) {
                flash('danger', 'Error: ' . $e->getMessage());
            }
            header('Location: ' . BASE_URL . '/admin/assign-course');
            exit;
        }

        $assignments = $this->admin->getCourseAssignments();
        $courses = $this->admin->getCourses();
        $teachers = $this->admin->getTeachers();
        $departments = $this->admin->getDepartments();
        $students = $this->admin->getStudents();
        $flash = getFlash();
        require_once __DIR__ . '/../View/admin/assign_course.php';
    }

    public function marksApproval() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            try {
                if ($action === 'approve') {
                    $assignment_id = (int)$_POST['assignment_id'];
                    $this->admin->approveAndPublishMarks($assignment_id, $_SESSION['user_id']);
                    flash('success', 'Marks approved and locked! Official results published to students.');
                } elseif ($action === 'admin_override') {
                    $result_id = (int)$_POST['result_id'];
                    $total = (float)$_POST['grand_total'];
                    $remarks = sanitize($_POST['remarks'] ?? 'Admin correction');
                    $this->admin->updateApprovedMark($result_id, $total, $remarks, $_SESSION['user_id']);
                    flash('success', 'Official marks overridden and recalculation complete.');
                }
            } catch (Exception $e) {
                flash('danger', 'Error: ' . $e->getMessage());
            }
            header('Location: ' . BASE_URL . '/admin/marks-approval');
            exit;
        }

        $view_id = $_GET['assignment_id'] ?? null;
        $submission_details = null;
        $enrolled_students = [];

        if ($view_id) {
            $submission_details = (new TeacherModel())->getAssignmentDetails($view_id);
            $enrolled_students = $this->admin->getEnrollmentsForAssignment($view_id);
        }

        $submitted_list = $this->admin->getSubmittedAssignments();
        $flash = getFlash();
        require_once __DIR__ . '/../View/admin/marks_approval.php';
    }

    public function retakeAppeals() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $appeal_id = (int)$_POST['appeal_id'];
            $status = $_POST['status']; // approved or rejected
            $remarks = sanitize($_POST['admin_remarks'] ?? '');

            try {
                $this->admin->reviewRetakeAppeal($appeal_id, $status, $remarks, $_SESSION['user_id']);
                flash('success', "Retake appeal #$appeal_id marked as " . ucfirst($status) . ".");
            } catch (Exception $e) {
                flash('danger', 'Error: ' . $e->getMessage());
            }
            header('Location: ' . BASE_URL . '/admin/retake-appeals');
            exit;
        }

        $filter_status = $_GET['status'] ?? null;
        $appeals = $this->admin->getRetakeAppeals($filter_status);
        $flash = getFlash();
        require_once __DIR__ . '/../View/admin/retake_appeals.php';
    }

    public function resetPasswords() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = (int)$_POST['user_id'];
            $new_password = $_POST['new_password'] ?? '';

            if (strlen($new_password) < 6) {
                flash('danger', 'Password must be at least 6 characters.');
            } else {
                $res = $this->auth->adminResetPassword($user_id, $new_password, $_SESSION['user_id']);
                if ($res) {
                    flash('success', 'User password successfully reset without requiring their previous password.');
                } else {
                    flash('danger', 'Failed to reset password.');
                }
            }
            header('Location: ' . BASE_URL . '/admin/reset-passwords');
            exit;
        }

        $users = $this->admin->getAllUsersForReset();
        $flash = getFlash();
        require_once __DIR__ . '/../View/admin/reset_passwords.php';
    }

    public function announcements() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'add') {
                $this->admin->addAnnouncement($_POST['title'], $_POST['content'], $_POST['target_role'], $_SESSION['user_id']);
                flash('success', 'Announcement published.');
            } elseif ($action === 'delete') {
                $this->admin->deleteAnnouncement($_POST['announcement_id']);
                flash('success', 'Announcement removed.');
            }
            header('Location: ' . BASE_URL . '/admin/announcements');
            exit;
        }

        $announcements = $this->auth->getAnnouncements('all');
        $flash = getFlash();
        require_once __DIR__ . '/../View/admin/announcements.php';
    }

    public function reports() {
        $year = $_GET['academic_year'] ?? '2023-2024';
        $sem = $_GET['semester'] ?? 4;
        $dept = $_GET['dept_id'] ?? null;

        $departments = $this->admin->getDepartments();
        $class_reports = $this->result->getClassPerformanceReport($year, $sem, $dept);
        $grade_dist = $this->result->getGradeDistribution($year, $sem, $dept);
        $flash = getFlash();
        require_once __DIR__ . '/../View/admin/reports.php';
    }
}