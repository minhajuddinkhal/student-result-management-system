<?php
// config/constants.php

define('APP_NAME', 'University Student Result Management System');
define('APP_SHORT', 'SRMS');
define('BASE_URL', '/trial');

// Standard University Grading Scale
// 80% and above -> A+ (4.00)
// 75% to <80% -> A (3.75)
// 70% to <75% -> A- (3.50)
// 65% to <70% -> B+ (3.25)
// 60% to <65% -> B (3.00)
// 55% to <60% -> B- (2.75)
// 50% to <55% -> C+ (2.50)
// 45% to <50% -> C (2.25)
// 40% to <45% -> D (2.00)
// Less than 40% -> F (0.00)

function getGradeInfo($percentage) {
    $p = round((float)$percentage, 2);
    if ($p >= 80.0) {
        return ['grade' => 'A+', 'point' => 4.00, 'remark' => 'Outstanding', 'badge' => 'bg-success'];
    } elseif ($p >= 75.0) {
        return ['grade' => 'A', 'point' => 3.75, 'remark' => 'Excellent', 'badge' => 'bg-success'];
    } elseif ($p >= 70.0) {
        return ['grade' => 'A-', 'point' => 3.50, 'remark' => 'Very Good', 'badge' => 'bg-primary'];
    } elseif ($p >= 65.0) {
        return ['grade' => 'B+', 'point' => 3.25, 'remark' => 'Good', 'badge' => 'bg-info text-dark'];
    } elseif ($p >= 60.0) {
        return ['grade' => 'B', 'point' => 3.00, 'remark' => 'Satisfactory', 'badge' => 'bg-info text-dark'];
    } elseif ($p >= 55.0) {
        return ['grade' => 'B-', 'point' => 2.75, 'remark' => 'Above Average', 'badge' => 'bg-secondary'];
    } elseif ($p >= 50.0) {
        return ['grade' => 'C+', 'point' => 2.50, 'remark' => 'Average', 'badge' => 'bg-warning text-dark'];
    } elseif ($p >= 45.0) {
        return ['grade' => 'C', 'point' => 2.25, 'remark' => 'Pass', 'badge' => 'bg-warning text-dark'];
    } elseif ($p >= 40.0) {
        return ['grade' => 'D', 'point' => 2.00, 'remark' => 'Conditional Pass', 'badge' => 'bg-danger text-white'];
    } else {
        return ['grade' => 'F', 'point' => 0.00, 'remark' => 'Fail', 'badge' => 'bg-danger'];
    }
}

// Student can retake course if Grade is less than C (< 2.25 Grade Point / < 45%)
function isRetakeEligible($grade_point, $letter_grade = '') {
    if ($letter_grade === 'F' || $letter_grade === 'D') {
        return true;
    }
    return ((float)$grade_point < 2.25);
}

// Phone Number Validation: 11 digits starting with 013, 014, 015, 016, 017, 018, 019
function validateBdPhone($phone) {
    $cleaned = preg_replace('/[^\d]/', '', $phone);
    if (preg_match('/^01[3-9]\d{8}$/', $cleaned)) {
        return $cleaned;
    }
    return false;
}