<?php
$page_title = 'Faculty Marks Entry & Finalization';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';

$is_approved = ($assignment['marks_status'] === 'approved');
$is_submitted = ($assignment['marks_status'] === 'submitted');
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div>
            <h6 class="m-0 fw-bold text-primary">
                <i class="bi bi-pencil-square me-2"></i>Marks Entry: <?= htmlspecialchars($assignment['course_code']) ?> - <?= htmlspecialchars($assignment['course_title']) ?>
            </h6>
            <small class="text-muted">
                Section: <?= $assignment['section'] ?> | Session: <?= $assignment['academic_year'] ?> | 
                Weight Split: Midterm <?= $assignment['mid_weight'] ?>% + Final Term <?= $assignment['final_weight'] ?>%
            </small>
        </div>
        <div>
            <?php if ($is_approved): ?>
                <span class="badge bg-success fs-6"><i class="bi bi-lock-fill me-1"></i> Approved by Administrator (Locked)</span>
            <?php elseif ($is_submitted): ?>
                <span class="badge bg-warning text-dark fs-6"><i class="bi bi-clock-history me-1"></i> Submitted to Admin for Approval</span>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/teacher/assessment-setup?assignment_id=<?= $assignment['assignment_id'] ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-gear me-1"></i> Reconfigure Assessment Split
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body p-0">
        <?php if ($is_approved): ?>
            <div class="alert alert-success m-3">
                <i class="bi bi-shield-check-fill me-2"></i>
                <strong>Marks Approved:</strong> These results have been approved by the Administrator and published to the official database. In accordance with university rules, marks cannot be modified by the instructor after approval (only Administrator can make corrections).
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/teacher/marks-entry?assignment_id=<?= $assignment['assignment_id'] ?>" method="POST">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th rowspan="2" style="width: 100px;">Roll No</th>
                            <th rowspan="2" style="width: 160px;">Student Name</th>
                            <!-- Component Headers -->
                            <?php foreach ($grid['components'] as $comp): ?>
                                <th>
                                    <span class="d-block small fw-bold"><?= htmlspecialchars($comp['component_name']) ?></span>
                                    <span class="badge bg-<?= $comp['term_type'] === 'mid' ? 'primary' : 'success' ?> bg-opacity-75" style="font-size: 0.7rem;">
                                        Max: <?= $comp['max_marks'] ?> (<?= $comp['weightage'] ?>%)
                                    </span>
                                </th>
                            <?php endforeach; ?>
                            <th rowspan="2" style="width: 90px;">Mid (<?= $assignment['mid_weight'] ?>%)</th>
                            <th rowspan="2" style="width: 90px;">Fin (<?= $assignment['final_weight'] ?>%)</th>
                            <th rowspan="2" style="width: 100px;">Total (100)</th>
                            <th rowspan="2" style="width: 80px;">Grade</th>
                            <th rowspan="2" style="width: 70px;">GP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($grid['students'])): ?>
                            <tr><td colspan="<?= count($grid['components']) + 7 ?>" class="text-center py-5 text-muted">No students currently enrolled in this course offering.</td></tr>
                        <?php else: ?>
                            <?php foreach ($grid['students'] as $st): ?>
                                <tr>
                                    <td class="fw-bold text-center"><?= htmlspecialchars($st['roll_no']) ?></td>
                                    <td>
                                        <span class="d-block fw-semibold"><?= htmlspecialchars($st['name']) ?></span>
                                        <?php if ($st['enrollment_type'] === 'retake'): ?>
                                            <span class="badge bg-danger" style="font-size: 0.65rem;">Retake</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Component Inputs -->
                                    <?php foreach ($grid['components'] as $comp): 
                                        $val = $grid['marks_map'][$st['enrollment_id']][$comp['component_id']] ?? '';
                                    ?>
                                        <td style="max-width: 110px;">
                                            <input type="number" 
                                                   step="0.25" 
                                                   name="marks[<?= $st['enrollment_id'] ?>][<?= $comp['component_id'] ?>]" 
                                                   class="form-control form-control-sm text-center" 
                                                   value="<?= $val ?>" 
                                                   min="0" 
                                                   max="<?= $comp['max_marks'] ?>" 
                                                   <?= $is_approved ? 'readonly' : '' ?>
                                                   placeholder="0 - <?= $comp['max_marks'] ?>"
                                                   required>
                                        </td>
                                    <?php endforeach; ?>

                                    <!-- Calculated preview columns -->
                                    <td class="text-center text-primary fw-bold"><?= number_format($st['mid_total'] ?? 0, 1) ?></td>
                                    <td class="text-center text-success fw-bold"><?= number_format($st['final_total'] ?? 0, 1) ?></td>
                                    <td class="text-center fw-bold fs-6"><?= number_format($st['grand_total'] ?? 0, 2) ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($st['letter_grade'])): ?>
                                            <?php $g_info = getGradeInfo($st['grand_total'] ?? 0); ?>
                                            <span class="badge <?= $g_info['badge'] ?>"><?= $st['letter_grade'] ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center fw-bold"><?= number_format($st['grade_point'] ?? 0, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!$is_approved): ?>
                <div class="card-footer bg-white p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <a href="<?= BASE_URL ?>/teacher/courses" class="btn btn-light">Back to Courses</a>
                    <div class="d-flex gap-2">
                        <button type="submit" name="save_draft" class="btn btn-outline-primary">
                            <i class="bi bi-save me-1"></i> Save Draft
                        </button>
                        <button type="submit" name="submit_approval" class="btn btn-primary" onclick="return confirm('Submit finalized marks to Administrator for approval? After approval, marks cannot be altered without admin permission.');">
                            <i class="bi bi-send-check me-1"></i> Submit for Admin Approval
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>