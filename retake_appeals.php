<?php
$page_title = 'Course Retake Appeals (< C Grade)';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <div>
            <h6 class="m-0 fw-bold text-danger"><i class="bi bi-arrow-repeat me-2"></i>Course Retake Applications</h6>
            <small class="text-muted">Eligible for courses where student received a grade below 'C' (&lt; 2.25 Grade Point / &lt; 45% - i.e. D or F)</small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Appeal ID</th>
                        <th>Student Details</th>
                        <th>Course</th>
                        <th>Previous Grade</th>
                        <th>Student Reason</th>
                        <th>Status</th>
                        <th>Admin Remarks</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appeals)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">No retake appeals found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($appeals as $app): ?>
                            <tr>
                                <td>#<?= $app['appeal_id'] ?></td>
                                <td>
                                    <span class="fw-bold d-block"><?= htmlspecialchars($app['student_name']) ?></span>
                                    <small class="text-muted"><?= htmlspecialchars($app['roll_no']) ?> | <?= htmlspecialchars($app['dept_name']) ?></small>
                                </td>
                                <td>
                                    <span class="fw-bold"><?= htmlspecialchars($app['course_code']) ?></span>
                                    <small class="d-block text-muted"><?= htmlspecialchars($app['course_title']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-danger fs-6"><?= $app['previous_grade'] ?></span>
                                    <small class="d-block text-muted"><?= $app['previous_grade_point'] ?> GP</small>
                                </td>
                                <td><small class="text-secondary"><?= htmlspecialchars($app['reason']) ?></small></td>
                                <td>
                                    <?php if ($app['status'] === 'pending'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending</span>
                                    <?php elseif ($app['status'] === 'approved'): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Approved & Re-assigned</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?= htmlspecialchars($app['admin_remarks'] ?? 'N/A') ?></small></td>
                                <td class="text-end">
                                    <?php if ($app['status'] === 'pending'): ?>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal<?= $app['appeal_id'] ?>">
                                            Review Appeal
                                        </button>

                                        <!-- Review Appeal Modal -->
                                        <div class="modal fade text-start" id="reviewModal<?= $app['appeal_id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <form action="<?= BASE_URL ?>/admin/retake-appeals" method="POST">
                                                    <input type="hidden" name="appeal_id" value="<?= $app['appeal_id'] ?>">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Review Retake Appeal #<?= $app['appeal_id'] ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Student: <strong><?= htmlspecialchars($app['student_name']) ?> (<?= htmlspecialchars($app['roll_no']) ?>)</strong></p>
                                                            <p>Course: <strong><?= htmlspecialchars($app['course_code']) ?> - <?= htmlspecialchars($app['course_title']) ?></strong></p>
                                                            <p>Current Grade: <span class="badge bg-danger"><?= $app['previous_grade'] ?> (<?= $app['previous_grade_point'] ?> GP)</span></p>
                                                            <div class="alert alert-light border">
                                                                <small class="fw-bold d-block text-muted">Student's Appeal Reason:</small>
                                                                <?= htmlspecialchars($app['reason']) ?>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Decision</label>
                                                                <select name="status" class="form-select" required>
                                                                    <option value="approved">Approve (Automatically re-assign course to student dashboard)</option>
                                                                    <option value="rejected">Reject Appeal</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Admin Remarks / Feedback</label>
                                                                <textarea name="admin_remarks" class="form-control" rows="2" placeholder="e.g. Approved for Summer retake batch."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Submit Decision</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Processed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>