<?php
$page_title = 'Student Course Withdrawal Requests';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-danger"><i class="bi bi-box-arrow-right me-2"></i>Course Withdrawal Applications</h6>
        <small class="text-muted">Students can request withdrawal for assigned courses; course instructor approval is required.</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Student Reason</th>
                        <th>Status</th>
                        <th>Faculty Remarks</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">No course withdrawal requests submitted.</td></tr>
                    <?php else: ?>
                        <?php foreach ($requests as $r): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($r['roll_no']) ?></td>
                                <td><?= htmlspecialchars($r['student_name']) ?></td>
                                <td>
                                    <span class="fw-bold"><?= htmlspecialchars($r['course_code']) ?></span>
                                    <small class="d-block text-muted"><?= htmlspecialchars($r['course_title']) ?> (Sec <?= $r['section'] ?>)</small>
                                </td>
                                <td><small class="text-secondary"><?= htmlspecialchars($r['reason']) ?></small></td>
                                <td>
                                    <?php if ($r['status'] === 'pending'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending</span>
                                    <?php elseif ($r['status'] === 'approved'): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?= htmlspecialchars($r['teacher_remarks'] ?? 'N/A') ?></small></td>
                                <td class="text-end">
                                    <?php if ($r['status'] === 'pending'): ?>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#withdrawModal<?= $r['request_id'] ?>">
                                            Review Request
                                        </button>

                                        <!-- Review Withdrawal Modal -->
                                        <div class="modal fade text-start" id="withdrawModal<?= $r['request_id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <form action="<?= BASE_URL ?>/teacher/withdraw-requests" method="POST">
                                                    <input type="hidden" name="request_id" value="<?= $r['request_id'] ?>">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Review Withdrawal Request</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Student: <strong><?= htmlspecialchars($r['student_name']) ?> (<?= htmlspecialchars($r['roll_no']) ?>)</strong></p>
                                                            <p>Course: <strong><?= htmlspecialchars($r['course_code']) ?> - <?= htmlspecialchars($r['course_title']) ?></strong></p>
                                                            <div class="alert alert-light border">
                                                                <small class="fw-bold d-block text-muted">Student Reason:</small>
                                                                <?= htmlspecialchars($r['reason']) ?>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Decision</label>
                                                                <select name="status" class="form-select" required>
                                                                    <option value="approved">Approve Withdrawal</option>
                                                                    <option value="rejected">Reject Request</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Faculty Remarks</label>
                                                                <textarea name="teacher_remarks" class="form-control" rows="2" placeholder="e.g. Approved as per academic council rules"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Save Decision</button>
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