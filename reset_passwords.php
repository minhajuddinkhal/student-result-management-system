<?php
$page_title = 'User Password Reset Management';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-key-fill me-2"></i>Administrator Direct Password Reset</h6>
        <small class="text-muted">Administrator can directly reset any teacher or student password without requiring their previous password.</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User Role</th>
                        <th>Identifier / ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone (11 Digits)</th>
                        <th>Status</th>
                        <th class="text-end">Reset Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <span class="badge bg-<?= $u['role'] === 'admin' ? 'dark' : ($u['role'] === 'teacher' ? 'success' : 'primary') ?>">
                                    <?= strtoupper($u['role']) ?>
                                </span>
                            </td>
                            <td><code><?= htmlspecialchars($u['identifier']) ?></code></td>
                            <td class="fw-bold"><?= htmlspecialchars($u['display_name']) ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><small><?= htmlspecialchars($u['email']) ?></small></td>
                            <td><code><?= htmlspecialchars($u['phone']) ?></code></td>
                            <td><span class="badge bg-<?= $u['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($u['status']) ?></span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#resetModal<?= $u['user_id'] ?>">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Reset Password
                                </button>

                                <!-- Reset Password Modal -->
                                <div class="modal fade text-start" id="resetModal<?= $u['user_id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="<?= BASE_URL ?>/admin/reset-passwords" method="POST">
                                            <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reset Password for <?= htmlspecialchars($u['display_name']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-info py-2 small">
                                                        <i class="bi bi-info-circle me-1"></i> As Administrator, you do not need the user's old password.
                                                    </div>
                                                    <p>Username: <strong><?= htmlspecialchars($u['username']) ?></strong></p>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">New Password</label>
                                                        <input type="password" name="new_password" class="form-control" placeholder="Enter new password (min 6 chars)" minlength="6" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Confirm Reset Password</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>