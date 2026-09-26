<?php
$page_title = 'Students Management';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-people-fill me-2"></i>Enrolled Students List</h6>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                <i class="bi bi-person-plus-fill me-1"></i> Register New Student
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card-body bg-light border-bottom py-2">
        <form method="GET" action="<?= BASE_URL ?>/admin/students" class="row g-2 align-items-center">
            <div class="col-auto">
                <span class="small fw-bold text-muted">Filter By:</span>
            </div>
            <div class="col-md-3">
                <select name="dept_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['dept_id'] ?>" <?= ($filter_dept == $d['dept_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['dept_code']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Semesters (1-8)</option>
                    <?php for ($s = 1; $s <= 8; $s++): ?>
                        <option value="<?= $s ?>" <?= ($filter_sem == $s) ? 'selected' : '' ?>>
                            Semester <?= $s ?> (Year <?= ceil($s/2) ?>)
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <a href="<?= BASE_URL ?>/admin/students" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Roll / Reg No</th>
                        <th>Student Name</th>
                        <th>Department</th>
                        <th>Current Semester</th>
                        <th>Session</th>
                        <th>Phone (11 Digits)</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td>
                                <span class="fw-bold d-block"><?= htmlspecialchars($s['roll_no']) ?></span>
                                <small class="text-muted"><?= htmlspecialchars($s['reg_no']) ?></small>
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($s['name']) ?></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($s['dept_code']) ?></span></td>
                            <td><span class="badge bg-primary">Semester <?= $s['current_semester'] ?> (Year <?= ceil($s['current_semester']/2) ?>)</span></td>
                            <td><?= htmlspecialchars($s['academic_session']) ?></td>
                            <td><code><?= htmlspecialchars($s['phone']) ?></code></td>
                            <td><small><?= htmlspecialchars($s['email']) ?></small></td>
                            <td>
                                <span class="badge bg-<?= $s['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($s['status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/reports/transcript-print?student_id=<?= $s['student_id'] ?>" target="_blank" class="btn btn-sm btn-outline-info me-1" title="View Full Transcript">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-secondary me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editStudentModal<?= $s['student_id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="<?= BASE_URL ?>/admin/students" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this student record?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="student_id" value="<?= $s['student_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Student Modal -->
                        <div class="modal fade" id="editStudentModal<?= $s['student_id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <form action="<?= BASE_URL ?>/admin/students" method="POST">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="student_id" value="<?= $s['student_id'] ?>">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Student Information</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Full Name</label>
                                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($s['name']) ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Department</label>
                                                    <select name="dept_id" class="form-select" required>
                                                        <?php foreach ($departments as $d): ?>
                                                            <option value="<?= $d['dept_id'] ?>" <?= $d['dept_id'] == $s['dept_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($d['dept_code']) ?> - <?= htmlspecialchars($d['dept_name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label fw-semibold">Current Semester (1-8)</label>
                                                    <select name="semester" class="form-select" required>
                                                        <?php for ($sem = 1; $sem <= 8; $sem++): ?>
                                                            <option value="<?= $sem ?>" <?= $sem == $s['current_semester'] ? 'selected' : '' ?>>
                                                                Semester <?= $sem ?> (Year <?= ceil($sem/2) ?>)
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label fw-semibold">Academic Session</label>
                                                    <input type="text" name="session" class="form-control" value="<?= htmlspecialchars($s['academic_session']) ?>" required>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label fw-semibold">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" <?= $s['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                        <option value="graduated" <?= $s['status'] === 'graduated' ? 'selected' : '' ?>>Graduated</option>
                                                        <option value="dropped" <?= $s['status'] === 'dropped' ? 'selected' : '' ?>>Dropped</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Email Address</label>
                                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($s['email']) ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">11-digit Phone (013-019)</label>
                                                    <input type="text" name="phone" class="form-control bd-phone-input" value="<?= htmlspecialchars($s['phone']) ?>" maxlength="11" required>
                                                    <div class="phone-feedback small text-muted"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
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

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?= BASE_URL ?>/admin/students" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Register New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Username for Login</label>
                            <input type="text" name="username" class="form-control" placeholder="e.g. student_sakib" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Temporary Password</label>
                            <input type="password" name="password" class="form-control" placeholder="At least 6 characters" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Roll Number (Unique)</label>
                            <input type="text" name="roll_no" class="form-control" placeholder="e.g. CSE-2301" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Registration Number</label>
                            <input type="text" name="reg_no" class="form-control" placeholder="e.g. REG-2023-089" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Student Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Sakib Al Hasan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="dept_id" class="form-select" required>
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $d): ?>
                                    <option value="<?= $d['dept_id'] ?>"><?= htmlspecialchars($d['dept_code']) ?> - <?= htmlspecialchars($d['dept_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Enrolling Semester (1-8)</label>
                            <select name="semester" class="form-select" required>
                                <?php for ($sem = 1; $sem <= 8; $sem++): ?>
                                    <option value="<?= $sem ?>">Semester <?= $sem ?> (Year <?= ceil($sem/2) ?>)</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Academic Session</label>
                            <input type="text" name="session" class="form-control" placeholder="e.g. 2024-2025" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="student@student.edu" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">11-digit Phone (013, 014, 015, 016, 017, 018, 019)</label>
                            <input type="text" name="phone" class="form-control bd-phone-input" placeholder="017xxxxxxxx" maxlength="11" required>
                            <div class="phone-feedback small text-muted"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Enroll Student</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>