<?php
$page_title = 'Course Withdrawal Requests';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-danger"><i class="bi bi-box-arrow-right me-2"></i>My Course Withdrawal Applications</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Request ID</th>
                        <th>Course</th>
                        <th>Assigned Instructor</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Instructor Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No withdrawal requests found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($requests as $r): ?>
                            <tr>
                                <td>#<?= $r['request_id'] ?></td>
                                <td>
                                    <span class="fw-bold"><?= htmlspecialchars($r['course_code']) ?></span>
                                    <small class="d-block text-muted"><?= htmlspecialchars($r['course_title']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($r['teacher_name']) ?></td>
                                <td><small class="text-secondary"><?= htmlspecialchars($r['reason']) ?></small></td>
                                <td>
                                    <?php if ($r['status'] === 'pending'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending Teacher Approval</span>
                                    <?php elseif ($r['status'] === 'approved'): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?= htmlspecialchars($r['teacher_remarks'] ?? 'N/A') ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>