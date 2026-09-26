<?php
$page_title = 'Full 8-Semester Academic Transcript & CGPA';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<!-- Transcript Summary Header Card -->
<div class="card shadow-sm mb-4 border-0" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); color: #fff;">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-warning text-dark mb-2 fw-bold">OFFICIAL TRANSCRIPT OF ACADEMIC RECORD</span>
                <h3 class="fw-bold mb-1"><?= htmlspecialchars($student_info['name']) ?></h3>
                <p class="mb-0 text-white-50">
                    Roll: <strong><?= htmlspecialchars($student_info['roll_no']) ?></strong> | 
                    Reg: <strong><?= htmlspecialchars($student_info['reg_no']) ?></strong> | 
                    Department: <strong><?= htmlspecialchars($student_info['dept_name']) ?></strong>
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="bg-white bg-opacity-10 p-3 rounded-3 d-inline-block border border-white border-opacity-25 text-start">
                    <small class="d-block text-uppercase text-white-50">Cumulative CGPA</small>
                    <h2 class="fw-bold mb-0 text-warning"><?= number_format($transcript['cgpa'], 2) ?> <span class="fs-6 text-white">/ 4.00</span></h2>
                    <small class="text-white-50"><?= $transcript['total_credits_earned'] ?> / <?= $transcript['total_credits_attempted'] ?> Credits Earned</small>
                </div>
                <div class="mt-2">
                    <a href="<?= BASE_URL ?>/student/print-marksheet" target="_blank" class="btn btn-warning btn-sm fw-bold shadow-sm">
                        <i class="bi bi-printer-fill me-1"></i> Print Official Transcript
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 8 Semesters Breakdown -->
<div class="row g-4 mb-4">
    <?php for ($s = 1; $s <= 8; $s++): 
        $has_sem_data = isset($transcript['semesters'][$s]);
        $sdata = $transcript['semesters'][$s] ?? null;
    ?>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-bookmark-check-fill me-1"></i> Semester <?= $s ?> (Year <?= ceil($s/2) ?>, Term <?= ($s % 2 == 1) ? 1 : 2 ?>)
                    </h6>
                    <?php if ($has_sem_data): ?>
                        <span class="badge bg-primary">SGPA: <?= number_format($sdata['sgpa'], 2) ?></span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Upcoming</span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <?php if ($has_sem_data): ?>
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="table-light small text-muted">
                                <tr>
                                    <th>Course</th>
                                    <th>Title</th>
                                    <th>Cr</th>
                                    <th>Marks</th>
                                    <th>Grade</th>
                                    <th>GP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sdata['courses'] as $sc): ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($sc['course_code']) ?></code></td>
                                        <td class="small fw-semibold"><?= htmlspecialchars($sc['course_title']) ?></td>
                                        <td><?= $sc['credit_hours'] ?></td>
                                        <td><?= number_format($sc['grand_total'], 1) ?></td>
                                        <td><span class="badge bg-<?= $sc['letter_grade'] === 'F' ? 'danger' : 'success' ?>"><?= $sc['letter_grade'] ?></span></td>
                                        <td class="fw-bold"><?= number_format($sc['grade_point'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-hourglass text-secondary d-block fs-4 mb-1"></i>
                            Results not yet published for Semester <?= $s ?>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endfor; ?>
</div>

<!-- Official Grading System Reference Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-secondary"><i class="bi bi-table me-2"></i>University Standard Grading Scale Reference</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered text-center align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Numerical Range</th>
                        <th>Letter Grade</th>
                        <th>Grade Point</th>
                        <th>Performance Assessment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>80% and above</td><td><span class="badge bg-success">A+</span> (A Plus)</td><td>4.00</td><td>Outstanding</td></tr>
                    <tr><td>75% to &lt; 80%</td><td><span class="badge bg-success">A</span> (A Regular)</td><td>3.75</td><td>Excellent</td></tr>
                    <tr><td>70% to &lt; 75%</td><td><span class="badge bg-primary">A-</span> (A Minus)</td><td>3.50</td><td>Very Good</td></tr>
                    <tr><td>65% to &lt; 70%</td><td><span class="badge bg-info text-dark">B+</span> (B Plus)</td><td>3.25</td><td>Good</td></tr>
                    <tr><td>60% to &lt; 65%</td><td><span class="badge bg-info text-dark">B</span> (B Regular)</td><td>3.00</td><td>Satisfactory</td></tr>
                    <tr><td>55% to &lt; 60%</td><td><span class="badge bg-secondary">B-</span> (B Minus)</td><td>2.75</td><td>Above Average</td></tr>
                    <tr><td>50% to &lt; 55%</td><td><span class="badge bg-warning text-dark">C+</span> (C Plus)</td><td>2.50</td><td>Average</td></tr>
                    <tr><td>45% to &lt; 50%</td><td><span class="badge bg-warning text-dark">C</span> (C Regular)</td><td>2.25</td><td>Pass (Minimum Acceptable)</td></tr>
                    <tr class="table-warning"><td>40% to &lt; 45%</td><td><span class="badge bg-danger">D</span></td><td>2.00</td><td>Conditional Pass (Retake Eligible)</td></tr>
                    <tr class="table-danger"><td>Less than 40%</td><td><span class="badge bg-danger">F</span> (Fail)</td><td>0.00</td><td>Fail (Retake Eligible)</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>