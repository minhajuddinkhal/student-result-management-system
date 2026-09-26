<?php
$page_title = 'My Assigned Courses';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-journals me-2"></i>Assigned Course Offerings & Assessment Status</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Course Code & Title</th>
                        <th>Credits</th>
                        <th>Semester & Session</th>
                        <th>Enrolled Students</th>
                        <th>Assessment Distribution</th>
                        <th>Marks Workflow</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assigned_courses)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">No courses assigned to your account.</td></tr>
                    <?php else: ?>
                        <?php foreach ($assigned_courses as $c): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold d-block"><?= htmlspecialchars($c['course_code']) ?></span>
                                    <span class="text-muted"><?= htmlspecialchars($c['course_title']) ?> (Section: <?= $c['section'] ?>)</span>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= number_format($c['credit_hours'], 2) ?> Cr</span></td>
                                <td><span class="badge bg-primary">Semester <?= $c['semester'] ?></span> (<?= htmlspecialchars($c['academic_year']) ?>)</td>
                                <td><span class="badge bg-info text-dark"><?= $c['student_count'] ?> Enrolled</span></td>
                                <td>
                                    <?php if ($c['is_configured']): ?>
                                        <span class="badge bg-success">Midterm <?= $c['mid_weight'] ?>% / Final <?= $c['final_weight'] ?>%</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Not Configured</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($c['marks_status'] === 'approved'): ?>
                                        <span class="badge bg-success"><i class="bi bi-lock-fill"></i> Approved & Locked</span>
                                    <?php elseif ($c['marks_status'] === 'submitted'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Submitted to Admin</span>
                                    <?php elseif ($c['marks_status'] === 'draft'): ?>
                                        <span class="badge bg-secondary">Draft</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Not Started</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="<?= BASE_URL ?>/teacher/assessment-setup?assignment_id=<?= $c['assignment_id'] ?>" class="btn btn-sm btn-outline-secondary" title="Configure Assessment Weights">
                                            <i class="bi bi-gear"></i> Setup
                                        </a>
                                        <a href="<?= BASE_URL ?>/teacher/marks-entry?assignment_id=<?= $c['assignment_id'] ?>" class="btn btn-sm btn-primary" title="Marks Entry">
                                            <i class="bi bi-pencil-square"></i> Marks
                                        </a>
                                    </div>
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