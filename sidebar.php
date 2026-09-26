<?php
$role = $_SESSION['role'] ?? '';
$current_url = trim($_GET['url'] ?? '', '/');
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-mortarboard-fill text-warning fs-4"></i>
        <span><?= APP_SHORT ?> Portal</span>
    </div>
    
    <ul class="sidebar-nav">
        <?php if ($role === 'admin'): ?>
            <li class="nav-header">Administration</li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'admin/dashboard' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/dashboard">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'admin/departments' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/departments">
                    <i class="bi bi-building"></i> Departments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'admin/teachers' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/teachers">
                    <i class="bi bi-person-badge-fill"></i> Teachers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'admin/students' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/students">
                    <i class="bi bi-people-fill"></i> Students
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'admin/courses' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/courses">
                    <i class="bi bi-journal-bookmark-fill"></i> Course Catalog
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'admin/assign-course' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/assign-course">
                    <i class="bi bi-card-checklist"></i> Assign Courses
                </a>
            </li>

            <li class="nav-header">Result Management</li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'admin/marks-approval' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/marks-approval">
                    <i class="bi bi-check2-circle"></i> Marks Approval
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'admin/retake-appeals' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/retake-appeals">
                    <i class="bi bi-arrow-repeat"></i> Retake Appeals
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'admin/reports' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/reports">
                    <i class="bi bi-graph-up-arrow"></i> Reports & Analytics
                </a>
            </li>

            <li class="nav-header">System Settings</li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'admin/reset-passwords' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/reset-passwords">
                    <i class="bi bi-key-fill"></i> User Password Reset
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'admin/announcements' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/announcements">
                    <i class="bi bi-megaphone-fill"></i> Announcements
                </a>
            </li>

        <?php elseif ($role === 'teacher'): ?>
            <li class="nav-header">Faculty Menu</li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'teacher/dashboard' ? 'active' : '' ?>" href="<?= BASE_URL ?>/teacher/dashboard">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'teacher/courses' ? 'active' : '' ?>" href="<?= BASE_URL ?>/teacher/courses">
                    <i class="bi bi-journals"></i> Assigned Courses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'teacher/withdraw-requests' ? 'active' : '' ?>" href="<?= BASE_URL ?>/teacher/withdraw-requests">
                    <i class="bi bi-box-arrow-right"></i> Withdraw Requests
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'teacher/profile' ? 'active' : '' ?>" href="<?= BASE_URL ?>/teacher/profile">
                    <i class="bi bi-person-lines-fill"></i> Contact & Profile
                </a>
            </li>

        <?php elseif ($role === 'student'): ?>
            <li class="nav-header">Student Portal</li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'student/dashboard' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/dashboard">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'student/courses' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/courses">
                    <i class="bi bi-journal-text"></i> My Courses & Faculty
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'student/results' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/results">
                    <i class="bi bi-award-fill"></i> Semester Results
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'student/transcript' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/transcript">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i> Academic Transcript
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_url === 'student/retake-appeal' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/retake-appeal">
                    <i class="bi bi-arrow-repeat"></i> Course Retake Appeal
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-header">Account</li>
        <li class="nav-item">
            <a class="nav-link <?= $current_url === 'change-password' ? 'active' : '' ?>" href="<?= BASE_URL ?>/change-password">
                <i class="bi bi-shield-lock-fill"></i> Change Password
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="<?= BASE_URL ?>/logout">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </li>
    </ul>
</aside>