<?php
// Model/ResultModel.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class ResultModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getClassPerformanceReport($academic_year, $semester, $dept_id = null) {
        $sql = "SELECT ca.assignment_id, c.course_code, c.course_title, c.credit_hours, t.name as teacher_name, d.dept_name,
                COUNT(fr.result_id) as total_students,
                SUM(CASE WHEN fr.letter_grade != 'F' THEN 1 ELSE 0 END) as passed_count,
                SUM(CASE WHEN fr.letter_grade = 'F' THEN 1 ELSE 0 END) as failed_count,
                AVG(fr.grand_total) as avg_marks,
                AVG(fr.grade_point) as avg_gp
                FROM course_assignments ca
                JOIN courses c ON ca.course_id = c.course_id
                JOIN teachers t ON ca.teacher_id = t.teacher_id
                JOIN departments d ON c.dept_id = d.dept_id
                LEFT JOIN final_results fr ON (ca.assignment_id = fr.assignment_id AND fr.is_published = 1)
                WHERE ca.academic_year = ? AND ca.semester = ?";
        $params = [$academic_year, $semester];
        if ($dept_id) {
            $sql .= " AND c.dept_id = ?";
            $params[] = $dept_id;
        }
        $sql .= " GROUP BY ca.assignment_id ORDER BY c.course_code ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getGradeDistribution($academic_year, $semester, $dept_id = null) {
        $sql = "SELECT fr.letter_grade, COUNT(*) as count
                FROM final_results fr
                JOIN course_assignments ca ON fr.assignment_id = ca.assignment_id
                JOIN courses c ON ca.course_id = c.course_id
                WHERE ca.academic_year = ? AND ca.semester = ? AND fr.is_published = 1";
        $params = [$academic_year, $semester];
        if ($dept_id) {
            $sql .= " AND c.dept_id = ?";
            $params[] = $dept_id;
        }
        $sql .= " GROUP BY fr.letter_grade ORDER BY fr.grade_point DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}