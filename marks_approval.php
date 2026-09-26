<?php
$page_title = 'Faculty Marks Approval & Result Publishing';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="row g-4">
    <!-- Submissions List -->
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-inbox-fill me-2"></i>Submitted Marks by Faculty</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($submitted_list)): ?>
                        <div class="text-center py-5 text-muted">No marks submitted for approval.</div>
                    <?php else: ?>
                        <?php foreach ($submitted_list as $sub): ?>
                            <a href="<?= BASE_URL ?>/admin/marks-approval?assignment_id=<?= $sub['assignment_id'] ?>" 
                               class="list-group-item list-group-item-action p-3 <?= ($view_id == $sub['assignment_id']) ? 'active' : '' ?>">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold mb-0"><?= htmlspecialchars($sub['course_code']) ?> (Sec <?= $sub['section'] ?>)</h6>
                                    <?php if ($sub['marks_status'] === 'submitted'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Awaiting Review</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><i class="bi bi-lock-fill"></i> Approved & Published</span>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-1 text-truncate" style="font-size: 0.9rem;"><?= htmlspecialchars($sub['course_title']) ?></p>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span><i class="bi bi-person me-1"></i><?= htmlspecialchars($sub['teacher_name']) ?></span>
                                    <span><i class="bi bi-people me-1"></i><?= $sub['total_students'] ?> Students</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Review & Approval Panel -->
    <div class="col-lg-7">
        <?php if ($submission_details): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h6 class="m-0 fw-bold text-primary"><?= htmlspecialchars($submission_details['course_code']) ?> - <?= htmlspecialchars($submission_details['course_title']) ?></h6>
                        <small class="text-muted">Faculty: <?= htmlspecialchars($submission_details['teacher_name']) ?> | Sem <?= $submission_details['semester'] ?> | Session <?= $submission_details['academic_year'] ?></small>
                    </div>

                    <?php if ($submission_details['marks_status'] === 'submitted'): ?>
                        <form action="<?= BASE_URL ?>/admin/marks-approval" method="POST" onsubmit="return confirm('Approve and publish these marks? This will lock the marks from teacher editing and make results official.');">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="assignment_id" value="<?= $submission_details['assignment_id'] ?>">
                            <button type="submit" class="btn btn-success btn-sm shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i> Approve & Publish Results
                            </button>
                        </form>
                    <?php else: ?>
                        <span class="badge bg-success fs-6"><i class="bi bi-shield-check me-1"></i> Official Approved & Locked</span>
                    <?php endif; ?>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Roll No</th>
                                    <th>Student Name</th>
                                    <th>Grand Total</th>
                                    <th>Letter Grade</th>
                                    <th>Grade Point</th>
                                    <th>Pass/Fail</th>
                                    <th class="text-end">Admin Override</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrolled_students as $st): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($st['roll_no']) ?></td>
                                        <td><?= htmlspecialchars($st['student_name']) ?></td>
                                        <td><strong><?= number_format($st['grand_total'], 2) ?></strong> / 100</td>
                                        <td>
                                            <?php $g_info = getGradeInfo($st['grand_total']); ?>
                                            <span class="badge <?= $g_info['badge'] ?>"><?= $st['letter_grade'] ?></span>
                                        </td>
                                        <td><?= number_format($st['grade_point'], 2) ?></td>
                                        <td>
                                            <?php if ($st['letter_grade'] !== 'F'): ?>
                                                <span class="badge bg-success">Passed</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Failed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-xs btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#overrideModal<?= $st['enrollment_id'] ?>">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Admin Mark Override Modal -->
                                    <div class="modal fade" id="overrideModal<?= $st['enrollment_id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="<?= BASE_URL ?>/admin/marks-approval" method="POST">
                                                <input type="hidden" name="action" value="admin_override">
                                                <input type="hidden" name="assignment_id" value="<?= $submission_details['assignment_id'] ?>">
                                                <input type="hidden" name="result_id" value="<?= $st['enrollment_id'] ?>">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Admin Marks Correction</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Student: <strong><?= htmlspecialchars($st['student_name']) ?> (<?= htmlspecialchars($st['roll_no']) ?>)</strong></p>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Grand Total Marks (0 - 100)</label>
                                                            <input type="number" step="0.1" name="grand_total" class="form-control" value="<?= $st['grand_total'] ?>" min="0" max="100" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Correction Reason / Remarks</label>
                                                            <textarea name="remarks" class="form-control" rows="2" placeholder="e.g. Re-evaluation / scrutiny correction" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Override</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-arrow-left-circle fs-1 text-primary d-block mb-2"></i>
                    <h5>Select a course submission from the left panel</h5>
                    <p class="m-0">You can view the detailed marks sheet, approve & lock the marks, or perform admin corrections.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>