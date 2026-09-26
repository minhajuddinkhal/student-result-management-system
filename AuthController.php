<?php
// Controller/AuthController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/AuthModel.php';

class AuthController {
    private $auth;

    public function __construct() {
        $this->auth = new AuthModel();
    }

    public function showLogin() {
        if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
            header('Location: ' . BASE_URL . '/' . $_SESSION['role'] . '/dashboard');
            exit;
        }
        $flash = getFlash();
        $announcements = $this->auth->getAnnouncements('all');
        require_once __DIR__ . '/../View/auth/login.php';
    }

    public function loginAction() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            flash('danger', 'Please enter both username/email and password.');
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $user = $this->auth->login($username, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'] ?? $user['username'];
            $_SESSION['email'] = $user['email'] ?? '';
            $_SESSION['phone'] = $user['phone'] ?? '';
            $_SESSION['details'] = $user;

            flash('success', "Welcome back, " . htmlspecialchars($_SESSION['name']) . "!");
            header('Location: ' . BASE_URL . '/' . $user['role'] . '/dashboard');
            exit;
        } else {
            flash('danger', 'Invalid login credentials or account is inactive.');
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public function logoutAction() {
        if (isset($_SESSION['user_id'])) {
            $this->auth->logAudit($_SESSION['user_id'], 'User Logout', 'Logged out');
        }
        session_unset();
        session_destroy();
        session_start();
        flash('info', 'You have been successfully logged out.');
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public function showChangePassword() {
        requireLogin();
        $flash = getFlash();
        require_once __DIR__ . '/../View/common/change_password.php';
    }

    public function changePasswordAction() {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/change-password');
            exit;
        }

        $old = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($new) < 6) {
            flash('danger', 'New password must be at least 6 characters long.');
            header('Location: ' . BASE_URL . '/change-password');
            exit;
        }

        if ($new !== $confirm) {
            flash('danger', 'New password and confirmation password do not match.');
            header('Location: ' . BASE_URL . '/change-password');
            exit;
        }

        $res = $this->auth->changePassword($_SESSION['user_id'], $old, $new);
        if ($res) {
            flash('success', 'Your password has been changed successfully.');
            header('Location: ' . BASE_URL . '/' . $_SESSION['role'] . '/dashboard');
            exit;
        } else {
            flash('danger', 'Current password is incorrect.');
            header('Location: ' . BASE_URL . '/change-password');
            exit;
        }
    }

    public function showNotifications() {
        requireLogin();
        $notifications = $this->auth->getUserNotifications($_SESSION['user_id']);
        $this->auth->markNotificationsRead($_SESSION['user_id']);
        $flash = getFlash();
        require_once __DIR__ . '/../View/common/notifications.php';
    }
}