<?php
// Model/AuthModel.php
require_once __DIR__ . '/../config/db.php';

class AuthModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function login($username_or_email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active' LIMIT 1");
        $stmt->execute([$username_or_email, $username_or_email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $user_details = ['user_id' => $user['user_id'], 'username' => $user['username'], 'role' => $user['role'], 'email' => $user['email'], 'phone' => $user['phone']];
            
            if ($user['role'] === 'teacher') {
                $st = $this->db->prepare("SELECT teacher_id, teacher_code, name, dept_id, designation, office_room FROM teachers WHERE user_id = ?");
                $st->execute([$user['user_id']]);
                $t = $st->fetch();
                if ($t) {
                    $user_details = array_merge($user_details, $t);
                }
            } elseif ($user['role'] === 'student') {
                $ss = $this->db->prepare("SELECT s.student_id, s.roll_no, s.reg_no, s.name, s.dept_id, s.current_semester, s.academic_session, d.dept_name, d.dept_code 
                                          FROM students s JOIN departments d ON s.dept_id = d.dept_id WHERE s.user_id = ?");
                $ss->execute([$user['user_id']]);
                $s = $ss->fetch();
                if ($s) {
                    $user_details = array_merge($user_details, $s);
                }
            } else {
                $user_details['name'] = 'System Administrator';
            }

            $this->logAudit($user['user_id'], 'User Login', 'Logged in successfully from ' . ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'));
            return $user_details;
        }
        return false;
    }

    public function changePassword($user_id, $old_password, $new_password) {
        $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($old_password, $user['password_hash'])) {
            $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $up = $this->db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $up->execute([$new_hash, $user_id]);
            $this->logAudit($user_id, 'Password Change', 'User changed their own password');
            return true;
        }
        return false;
    }

    public function adminResetPassword($user_id, $new_password, $admin_id) {
        $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $res = $stmt->execute([$new_hash, $user_id]);
        if ($res) {
            $this->logAudit($admin_id, 'Admin Password Reset', "Admin reset password for user ID: $user_id without old password");
            $this->createNotification($user_id, 'Password Reset By Administrator', 'Your account password has been reset by the system administrator.', 'warning');
            return true;
        }
        return false;
    }

    public function logAudit($user_id, $action, $details = '') {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt = $this->db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $action, $details, $ip]);
    }

    public function createNotification($user_id, $title, $message, $type = 'info', $link = null) {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, title, message, type, link) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $message, $type, $link]);
    }

    public function getUserNotifications($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function markNotificationsRead($user_id) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        return $stmt->execute([$user_id]);
    }

    public function getAnnouncements($role = 'all') {
        $stmt = $this->db->prepare("SELECT a.*, u.username as creator_name FROM announcements a 
                                    JOIN users u ON a.created_by = u.user_id 
                                    WHERE a.target_role = 'all' OR a.target_role = ? 
                                    ORDER BY a.created_at DESC LIMIT 10");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }
}