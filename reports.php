<?php
$page_title = 'Academic Reports & Performance Analytics';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-filter-circle me-2"></i>Filter Performance Reports</h6>
    </div>
    <div class="card-body bg-light border-bottom">
        <form method="GET" action="<?= BASE_URL ?>/admin/reports" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Academic Session</label>
                <input type="text" name="academic_year" class="form-control form-control-sm" value="<?= htmlspecialchars($year) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Semester (1-8)</label>
                <select name="semester" class="form-select form-select-sm">
                    <?php for ($s = 1; $s <= 8; $s++): ?>
                        <option value="<?= $s ?>" <?= ($sem == $s) ? 'selected' : '' ?>>Semester <?= $s ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Department</label>
                <select name="dept_id" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['dept_id'] ?>" <?= ($dept == $d['dept_id']) ? 'selected' : '' ?>><?= htmlspecialchars($d['dept_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i> Generate</button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Faculty</th>
                        <th>Students Enrolled</th>
                        <th>Passed</th>
                        <th>Failed</th>
                        <th>Avg Marks</th>
                        <th>Avg GP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($class_reports)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">No result records found for this query.</td></tr>
                    <?php else: ?>
                        <?php foreach ($class_reports as $cr): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($cr['course_code']) ?></span></td>
                                <td class="fw-bold"><?= htmlspecialchars($cr['course_title']) ?></td>
                                <td><?= htmlspecialchars($cr['teacher_name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= $cr['total_students'] ?></span></td>
                                <td><span class="badge bg-success"><?= $cr['passed_count'] ?></span></td>
                                <td><span class="badge bg-danger"><?= $cr['failed_count'] ?></span></td>
                                <td><?= number_format($cr['avg_marks'], 2) ?>%</td>
                                <td><strong><?= number_format($cr['avg_gp'], 2) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>