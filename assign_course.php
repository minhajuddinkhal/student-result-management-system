<?php
$page_title = 'Assign Courses & Enrollments';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="row g-4 mb-4">
    <!-- Assign Teacher to Course Form -->
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-person-check-fill me-2"></i>Assign Teacher to Course</h6>
            </div>
            <div class="card-body p-3">
                <form action="<?= BASE_URL ?>/admin/assign-course" method="POST">
                    <input type="hidden" name="action" value="assign_teacher">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Course</label>
                        <select name="course_id" class="form-select" required>
                            <option value="">-- Choose Course --</option>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?= $c['course_id'] ?>">
                                    [Sem <?= $c['semester'] ?>] <?= htmlspecialchars($c['course_code']) ?> - <?= htmlspecialchars($c['course_title']) ?> (<?= $c['dept_code'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assigned Faculty / Teacher</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">-- Choose Teacher --</option>
                            <?php foreach ($teachers as $t): ?>
                                <option value="<?= $t['teacher_id'] ?>">
                                    <?= htmlspecialchars($t['name']) ?> (<?= htmlspecialchars($t['dept_code']) ?> - <?= htmlspecialchars($t['designation']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Academic Session / Year</label>
                            <input type="text" name="academic_year" class="form-control" value="2023-2024" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Semester (1 to 8)</label>
                            <select name="semester" class="form-select" required>
                                <?php for ($s = 1; $s <= 8; $s++): ?>
                                    <option value="<?= $s ?>" <?= ($s == 4) ? 'selected' : '' ?>>Semester <?= $s ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Section</label>
                        <input type="text" name="section" class="form-control" value="A" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check2-circle me-1"></i> Assign Course Offering
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Batch Student Enrollment Form -->
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-success"><i class="bi bi-people-fill me-2"></i>Batch Enroll Students into Course Offering</h6>
            </div>
            <div class="card-body p-3">
                <form action="<?= BASE_URL ?>/admin/assign-course" method="POST">
                    <input type="hidden" name="action" value="enroll_batch">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Active Course Offering</label>
                        <select name="assignment_id" class="form-select" required>
                            <option value="">-- Choose Assigned Course --</option>
                            <?php foreach ($assignments as $a): ?>
                                <option value="<?= $a['assignment_id'] ?>">
                                    <?= htmlspecialchars($a['course_code']) ?> - <?= htmlspecialchars($a['course_title']) ?> (Sec: <?= $a['section'] ?>, Teacher: <?= htmlspecialchars($a['teacher_name']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Target Department</label>
                            <select name="dept_id" class="form-select" required>
                                <?php foreach ($departments as $d): ?>
                                    <option value="<?= $d['dept_id'] ?>"><?= htmlspecialchars($d['dept_code']) ?> - <?= htmlspecialchars($d['dept_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Target Semester (1 to 8)</label>
                            <select name="semester" class="form-select" required>
                                <?php for ($s = 1; $s <= 8; $s++): ?>
                                    <option value="<?= $s ?>" <?= ($s == 4) ? 'selected' : '' ?>>Semester <?= $s ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Academic Session / Year</label>
                        <input type="text" name="academic_year" class="form-control" value="2023-2024" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-person-plus-fill me-1"></i> Auto-Enroll All Active Students of This Semester
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Active Course Offerings Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-list-check me-2"></i>Active Course Assignments & Offerings</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Course</th>
                        <th>Semester</th>
                        <th>Assigned Faculty</th>
                        <th>Section & Session</th>
                        <th>Enrolled Students</th>
                        <th>Assessment Setup</th>
                        <th>Marks Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assignments)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No course assignments yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($assignments as $as): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold d-block"><?= htmlspecialchars($as['course_code']) ?></span>
                                    <small class="text-muted"><?= htmlspecialchars($as['course_title']) ?></small>
                                </td>
                                <td><span class="badge bg-primary">Sem <?= $as['semester'] ?></span></td>
                                <td>
                                    <span class="fw-bold d-block"><?= htmlspecialchars($as['teacher_name']) ?></span>
                                    <small class="text-muted"><a href="mailto:<?= htmlspecialchars($as['teacher_email']) ?>"><?= htmlspecialchars($as['teacher_email']) ?></a></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">Sec <?= $as['section'] ?></span>
                                    <small class="d-block text-muted"><?= htmlspecialchars($as['academic_year']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark"><?= $as['enrolled_count'] ?> Enrolled</span>
                                </td>
                                <td>
                                    <?php if ($as['is_configured']): ?>
                                        <span class="badge bg-success">Configured (Mid <?= $as['mid_weight'] ?>% / Fin <?= $as['final_weight'] ?>%)</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending Setup</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($as['marks_status'] === 'approved'): ?>
                                        <span class="badge bg-success"><i class="bi bi-lock-fill"></i> Approved & Locked</span>
                                    <?php elseif ($as['marks_status'] === 'submitted'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Submitted to Admin</span>
                                    <?php elseif ($as['marks_status'] === 'draft'): ?>
                                        <span class="badge bg-secondary">Draft</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Not Started</span>
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