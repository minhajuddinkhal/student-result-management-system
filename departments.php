<?php
$page_title = 'Department Management';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-building me-2"></i>Academic Departments</h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDeptModal">
            <i class="bi bi-plus-lg me-1"></i> Add Department
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Department Name</th>
                        <th>Description</th>
                        <th>Teachers</th>
                        <th>Students</th>
                        <th>Courses</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments as $d): ?>
                        <tr>
                            <td><span class="badge bg-primary fs-6"><?= htmlspecialchars($d['dept_code']) ?></span></td>
                            <td class="fw-bold"><?= htmlspecialchars($d['dept_name']) ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($d['description']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= $d['teacher_count'] ?></span></td>
                            <td><span class="badge bg-light text-dark border"><?= $d['student_count'] ?></span></td>
                            <td><span class="badge bg-light text-dark border"><?= $d['course_count'] ?></span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editDeptModal<?= $d['dept_id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="<?= BASE_URL ?>/admin/departments" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="dept_id" value="<?= $d['dept_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editDeptModal<?= $d['dept_id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="<?= BASE_URL ?>/admin/departments" method="POST">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="dept_id" value="<?= $d['dept_id'] ?>">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Department</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Department Code</label>
                                                <input type="text" name="dept_code" class="form-control" value="<?= htmlspecialchars($d['dept_code']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Department Name</label>
                                                <input type="text" name="dept_name" class="form-control" value="<?= htmlspecialchars($d['dept_name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Description</label>
                                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($d['description']) ?></textarea>
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

<!-- Add Modal -->
<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= BASE_URL ?>/admin/departments" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Department Code (e.g. CSE, EEE, BBA)</label>
                        <input type="text" name="dept_code" class="form-control" placeholder="CSE" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Department Name</label>
                        <input type="text" name="dept_name" class="form-control" placeholder="Computer Science and Engineering" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief summary of department"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Department</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>