-- Student Result Management System (SRMS) Database Backup
-- Database: result_management

CREATE DATABASE IF NOT EXISTS `result_management` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `result_management`;

-- Users
CREATE TABLE IF NOT EXISTS `users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(60) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'teacher', 'student') NOT NULL,
    `phone` VARCHAR(15) NOT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Departments
CREATE TABLE IF NOT EXISTS `departments` (
    `dept_id` INT AUTO_INCREMENT PRIMARY KEY,
    `dept_code` VARCHAR(20) NOT NULL UNIQUE,
    `dept_name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Teachers
CREATE TABLE IF NOT EXISTS `teachers` (
    `teacher_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `teacher_code` VARCHAR(30) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(15) NOT NULL,
    `dept_id` INT NOT NULL,
    `designation` VARCHAR(100) DEFAULT 'Lecturer',
    `office_room` VARCHAR(50) DEFAULT 'Room 101',
    `bio` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`dept_id`) REFERENCES `departments`(`dept_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Students
CREATE TABLE IF NOT EXISTS `students` (
    `student_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `roll_no` VARCHAR(30) NOT NULL UNIQUE,
    `reg_no` VARCHAR(30) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(15) NOT NULL,
    `dept_id` INT NOT NULL,
    `current_semester` TINYINT NOT NULL DEFAULT 1,
    `academic_session` VARCHAR(30) NOT NULL,
    `status` ENUM('active', 'graduated', 'dropped', 'suspended') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`dept_id`) REFERENCES `departments`(`dept_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Courses (8 Semesters)
CREATE TABLE IF NOT EXISTS `courses` (
    `course_id` INT AUTO_INCREMENT PRIMARY KEY,
    `course_code` VARCHAR(20) NOT NULL UNIQUE,
    `course_title` VARCHAR(150) NOT NULL,
    `credit_hours` DECIMAL(3,2) NOT NULL DEFAULT 3.00,
    `dept_id` INT NOT NULL,
    `semester` TINYINT NOT NULL,
    `course_type` ENUM('theory', 'lab') DEFAULT 'theory',
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`dept_id`) REFERENCES `departments`(`dept_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Course Assignments
CREATE TABLE IF NOT EXISTS `course_assignments` (
    `assignment_id` INT AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT NOT NULL,
    `teacher_id` INT NOT NULL,
    `academic_year` VARCHAR(30) NOT NULL,
    `semester` TINYINT NOT NULL,
    `section` VARCHAR(10) DEFAULT 'A',
    `mid_weight` DECIMAL(5,2) DEFAULT 40.00,
    `final_weight` DECIMAL(5,2) DEFAULT 60.00,
    `is_configured` TINYINT(1) DEFAULT 0,
    `marks_status` ENUM('not_started', 'draft', 'submitted', 'approved') DEFAULT 'not_started',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_course_offering` (`course_id`, `academic_year`, `semester`, `section`),
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`course_id`) ON DELETE CASCADE,
    FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`teacher_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Assessment Components
CREATE TABLE IF NOT EXISTS `assessment_components` (
    `component_id` INT AUTO_INCREMENT PRIMARY KEY,
    `assignment_id` INT NOT NULL,
    `term_type` ENUM('mid', 'final') NOT NULL,
    `component_name` VARCHAR(100) NOT NULL,
    `max_marks` DECIMAL(5,2) NOT NULL DEFAULT 100.00,
    `weightage` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`assignment_id`) REFERENCES `course_assignments`(`assignment_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Enrollments
CREATE TABLE IF NOT EXISTS `enrollments` (
    `enrollment_id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `assignment_id` INT NOT NULL,
    `academic_year` VARCHAR(30) NOT NULL,
    `semester` TINYINT NOT NULL,
    `enrollment_type` ENUM('regular', 'retake') DEFAULT 'regular',
    `status` ENUM('enrolled', 'withdrawn', 'completed') DEFAULT 'enrolled',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_student_assignment` (`student_id`, `assignment_id`),
    FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE,
    FOREIGN KEY (`assignment_id`) REFERENCES `course_assignments`(`assignment_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Marks
CREATE TABLE IF NOT EXISTS `marks` (
    `mark_id` INT AUTO_INCREMENT PRIMARY KEY,
    `enrollment_id` INT NOT NULL,
    `component_id` INT NOT NULL,
    `student_id` INT NOT NULL,
    `obtained_marks` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_mark_entry` (`enrollment_id`, `component_id`),
    FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments`(`enrollment_id`) ON DELETE CASCADE,
    FOREIGN KEY (`component_id`) REFERENCES `assessment_components`(`component_id`) ON DELETE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Final Results
CREATE TABLE IF NOT EXISTS `final_results` (
    `result_id` INT AUTO_INCREMENT PRIMARY KEY,
    `enrollment_id` INT NOT NULL UNIQUE,
    `student_id` INT NOT NULL,
    `assignment_id` INT NOT NULL,
    `mid_total` DECIMAL(5,2) DEFAULT 0.00,
    `final_total` DECIMAL(5,2) DEFAULT 0.00,
    `grand_total` DECIMAL(5,2) DEFAULT 0.00,
    `percentage` DECIMAL(5,2) DEFAULT 0.00,
    `grade_point` DECIMAL(3,2) DEFAULT 0.00,
    `letter_grade` VARCHAR(5) DEFAULT 'F',
    `status` ENUM('draft', 'submitted', 'approved') DEFAULT 'draft',
    `approved_by` INT NULL,
    `approved_at` DATETIME NULL,
    `is_published` TINYINT(1) DEFAULT 0,
    `remarks` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments`(`enrollment_id`) ON DELETE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE,
    FOREIGN KEY (`assignment_id`) REFERENCES `course_assignments`(`assignment_id`) ON DELETE CASCADE,
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Withdraw Requests
CREATE TABLE IF NOT EXISTS `withdraw_requests` (
    `request_id` INT AUTO_INCREMENT PRIMARY KEY,
    `enrollment_id` INT NOT NULL UNIQUE,
    `student_id` INT NOT NULL,
    `assignment_id` INT NOT NULL,
    `reason` TEXT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `teacher_remarks` TEXT NULL,
    `reviewed_by` INT NULL,
    `reviewed_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments`(`enrollment_id`) ON DELETE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE,
    FOREIGN KEY (`assignment_id`) REFERENCES `course_assignments`(`assignment_id`) ON DELETE CASCADE,
    FOREIGN KEY (`reviewed_by`) REFERENCES `teachers`(`teacher_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Retake Appeals
CREATE TABLE IF NOT EXISTS `retake_appeals` (
    `appeal_id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `previous_result_id` INT NOT NULL,
    `previous_grade` VARCHAR(5) NOT NULL,
    `previous_grade_point` DECIMAL(3,2) NOT NULL,
    `reason` TEXT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `admin_remarks` TEXT NULL,
    `reviewed_by` INT NULL,
    `reviewed_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`course_id`) ON DELETE CASCADE,
    FOREIGN KEY (`previous_result_id`) REFERENCES `final_results`(`result_id`) ON DELETE CASCADE,
    FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notifications
CREATE TABLE IF NOT EXISTS `notifications` (
    `notif_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('info', 'success', 'warning', 'danger') DEFAULT 'info',
    `is_read` TINYINT(1) DEFAULT 0,
    `link` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Announcements
CREATE TABLE IF NOT EXISTS `announcements` (
    `announcement_id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `content` TEXT NOT NULL,
    `target_role` ENUM('all', 'teacher', 'student') DEFAULT 'all',
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Audit Logs
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `log_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action` VARCHAR(100) NOT NULL,
    `details` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;