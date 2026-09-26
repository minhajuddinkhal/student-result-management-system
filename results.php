<?php
$page_title = 'Semester Results & Assessment Breakdown';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div>
            <h6 class="m-0 fw-bold text-primary"><i class="bi bi-award-fill me-2"></i>Official Semester Academic Results</h6>
            <small class="text-muted">Select a semester (1 to 8) to view detailed marks and grade points.</small>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/student/print-marksheet?semester=<?= $semester ?>" target="_blank" class="btn btn-sm btn-primary">
                <i class="bi bi-printer me-1"></i> Print Semester Marksheet
            </a>
        </div>
    </div>

    <!-- Semester Selector Tabs -->
    <div class="card-body bg-light border-bottom py-2">
        <ul class="nav nav-pills gap-1">
            <?php for ($s = 1; $s <= 8; $s++): ?>
                <li class="nav-item">
                    <a class="nav-link btn-sm <?= ($semester == $s) ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/results?semester=<?= $s ?>">
                        Semester <?= $s ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Credit Hours</th>
                        <th>Midterm Total</th>
                        <th>Final Term Total</th>
                        <th>Grand Total (100)</th>
                        <th>Letter Grade</th>
                        <th>Grade Point</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sem_credits = 0.0;
                    $sem_points = 0.0;
                    if (empty($results)): 
                    ?>
                        <tr><td colspan="9" class="text-center py-5 text-muted">No published results found for Semester <?= $semester ?>.</td></tr>
                    <?php else: ?>
                        <?php foreach ($results as $r): 
                            $cr = (float)$r['credit_hours'];
                            $gp = (float)$r['grade_point'];
                            $sem_credits += $cr;
                            $sem_points += ($cr * $gp);
                            $g_info = getGradeInfo($r['grand_total']);
                        ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($r['course_code']) ?></span></td>
                                <td class="fw-bold"><?= htmlspecialchars($r['course_title']) ?></td>
                                <td><?= number_format($cr, 2) ?></td>
                                <td><?= number_format($r['mid_total'], 2) ?></td>
                                <td><?= number_format($r['final_total'], 2) ?></td>
                                <td><strong><?= number_format($r['grand_total'], 2) ?></strong></td>
                                <td><span class="badge <?= $g_info['badge'] ?> fs-6"><?= $r['letter_grade'] ?></span></td>
                                <td class="fw-bold"><?= number_format($r['grade_point'], 2) ?></td>
                                <td>
                                    <?php if ($r['letter_grade'] !== 'F'): ?>
                                        <span class="badge bg-success">Passed</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Failed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($results) && $sem_credits > 0): 
                    $sgpa = round($sem_points / $sem_credits, 2);
                ?>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">Semester Summary:</td>
                            <td><?= number_format($sem_credits, 2) ?> Credits</td>
                            <td colspan="4" class="text-end">Semester GPA (SGPA):</td>
                            <td colspan="2" class="text-primary fs-5"><?= number_format($sgpa, 2) ?> / 4.00</td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>