<?php
$page_title = 'Faculty & Teachers Management';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-person-badge-fill me-2"></i>Faculty Members List</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
            <i class="bi bi-person-plus-fill me-1"></i> Register New Teacher
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Teacher Name & Designation</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Phone (11 digits)</th>
                        <th>Office Room</th>
                        <th>Assigned Courses</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $t): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($t['teacher_code']) ?></span></td>
                            <td>
                                <span class="fw-bold d-block"><?= htmlspecialchars($t['name']) ?></span>
                                <small class="text-muted"><?= htmlspecialchars($t['designation']) ?></small>
                            </td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($t['dept_code']) ?></span></td>
                            <td><a href="mailto:<?= htmlspecialchars($t['email']) ?>" class="text-decoration-none"><?= htmlspecialchars($t['email']) ?></a></td>
                            <td><code><?= htmlspecialchars($t['phone']) ?></code></td>
                            <td><?= htmlspecialchars($t['office_room']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= $t['assigned_courses_count'] ?> Courses</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editTeacherModal<?= $t['teacher_id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="<?= BASE_URL ?>/admin/teachers" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="teacher_id" value="<?= $t['teacher_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Teacher Modal -->
                        <div class="modal fade" id="editTeacherModal<?= $t['teacher_id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="<?= BASE_URL ?>/admin/teachers" method="POST">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="teacher_id" value="<?= $t['teacher_id'] ?>">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Teacher Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Full Name</label>
                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($t['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Department</label>
                                                <select name="dept_id" class="form-select" required>
                                                    <?php foreach ($departments as $d): ?>
                                                        <option value="<?= $d['dept_id'] ?>" <?= $d['dept_id'] == $t['dept_id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($d['dept_code']) ?> - <?= htmlspecialchars($d['dept_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Designation</label>
                                                <input type="text" name="designation" class="form-control" value="<?= htmlspecialchars($t['designation']) ?>" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Email Address</label>
                                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($t['email']) ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">11-digit Phone (013-019)</label>
                                                    <input type="text" name="phone" class="form-control bd-phone-input" value="<?= htmlspecialchars($t['phone']) ?>" maxlength="11" required>
                                                    <div class="phone-feedback small text-muted"></div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Office Room</label>
                                                <input type="text" name="office_room" class="form-control" value="<?= htmlspecialchars($t['office_room']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Bio / Specialization</label>
                                                <textarea name="bio" class="form-control" rows="2"><?= htmlspecialchars($t['bio']) ?></textarea>
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

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?= BASE_URL ?>/admin/teachers" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Register Faculty Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Username for Login</label>
                            <input type="text" name="username" class="form-control" placeholder="e.g. prof_john" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Temporary Password</label>
                            <input type="password" name="password" class="form-control" placeholder="At least 6 characters" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Teacher Code / ID</label>
                            <input type="text" name="teacher_code" class="form-control" placeholder="e.g. T-CSE-004" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Dr. Jane Smith" required>
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
                            <label class="form-label fw-semibold">Designation</label>
                            <input type="text" name="designation" class="form-control" placeholder="e.g. Assistant Professor" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Email Address (Visible to Students)</label>
                            <input type="email" name="email" class="form-control" placeholder="teacher@university.edu" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Phone (11 digits: 013, 014, 015, 016, 017, 018, 019)</label>
                            <input type="text" name="phone" class="form-control bd-phone-input" placeholder="017xxxxxxxx" maxlength="11" required>
                            <div class="phone-feedback small text-muted"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Office Room</label>
                            <input type="text" name="office_room" class="form-control" placeholder="e.g. Room 402, Building A">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Bio / Research Area</label>
                            <textarea name="bio" class="form-control" rows="2" placeholder="e.g. Machine Learning, Cloud Computing"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register Teacher</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>