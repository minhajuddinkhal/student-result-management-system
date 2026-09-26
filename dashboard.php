<?php
$page_title = 'Faculty Dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<!-- Teacher Profile Banner -->
<div class="card shadow-sm border-0 bg-primary text-white mb-4 p-4 rounded-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #0d9488 100%) !important;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="badge bg-white text-primary mb-2 fw-semibold"><?= htmlspecialchars($teacher_info['dept_name']) ?></span>
            <h3 class="fw-bold mb-1"><?= htmlspecialchars($teacher_info['name']) ?></h3>
            <p class="mb-0 opacity-75">
                <i class="bi bi-briefcase me-1"></i> <?= htmlspecialchars($teacher_info['designation']) ?> | 
                <i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($teacher_info['email']) ?> | 
                <i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($teacher_info['phone']) ?>
            </p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/teacher/profile" class="btn btn-light text-primary fw-semibold shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit Contact & Profile
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Assigned Courses -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-journals me-2"></i>My Assigned Courses</h6>
                <a href="<?= BASE_URL ?>/teacher/courses" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course</th>
                                <th>Semester</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($assigned_courses)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No courses assigned yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($assigned_courses as $c): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold d-block"><?= htmlspecialchars($c['course_code']) ?></span>
                                            <small class="text-muted"><?= htmlspecialchars($c['course_title']) ?> (Sec: <?= $c['section'] ?>)</small>
                                        </td>
                                        <td><span class="badge bg-primary">Sem <?= $c['semester'] ?></span></td>
                                        <td><span class="badge bg-light text-dark border"><?= $c['student_count'] ?> Enrolled</span></td>
                                        <td>
                                            <?php if ($c['marks_status'] === 'approved'): ?>
                                                <span class="badge bg-success"><i class="bi bi-lock-fill"></i> Approved & Locked</span>
                                            <?php elseif ($c['marks_status'] === 'submitted'): ?>
                                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Submitted</span>
                                            <?php elseif ($c['marks_status'] === 'draft'): ?>
                                                <span class="badge bg-secondary">Draft</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">Setup Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?php if (!$c['is_configured']): ?>
                                                <a href="<?= BASE_URL ?>/teacher/assessment-setup?assignment_id=<?= $c['assignment_id'] ?>" class="btn btn-sm btn-outline-warning">
                                                    <i class="bi bi-gear-fill me-1"></i> Setup
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= BASE_URL ?>/teacher/marks-entry?assignment_id=<?= $c['assignment_id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil-square me-1"></i> Marks
                                                </a>
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
    </div>

    <!-- Course Withdrawal Requests -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h6 class="m-0 fw-bold text-danger"><i class="bi bi-box-arrow-right me-2"></i>Withdrawal Requests</h6>
                <a href="<?= BASE_URL ?>/teacher/withdraw-requests" class="btn btn-sm btn-outline-danger">Review</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($withdraw_requests)): ?>
                        <div class="text-center py-4 text-muted">No withdrawal requests.</div>
                    <?php else: ?>
                        <?php foreach (array_slice($withdraw_requests, 0, 4) as $wr): ?>
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($wr['student_name']) ?></h6>
                                    <span class="badge bg-<?= $wr['status'] === 'pending' ? 'warning text-dark' : ($wr['status'] === 'approved' ? 'success' : 'danger') ?>">
                                        <?= ucfirst($wr['status']) ?>
                                    </span>
                                </div>
                                <small class="text-muted d-block"><?= htmlspecialchars($wr['course_code']) ?> (<?= htmlspecialchars($wr['roll_no']) ?>)</small>
                                <p class="small text-secondary mb-2 text-truncate"><?= htmlspecialchars($wr['reason']) ?></p>
                                <?php if ($wr['status'] === 'pending'): ?>
                                    <a href="<?= BASE_URL ?>/teacher/withdraw-requests" class="btn btn-xs btn-outline-primary btn-sm">Process Request</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>