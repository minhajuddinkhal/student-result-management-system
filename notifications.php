<?php
$page_title = 'Notifications & Alerts';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-bell me-2"></i>Notifications History</h6>
        <span class="badge bg-secondary"><?= count($notifications) ?> Alerts</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                <p class="m-0">No notifications found.</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $n): ?>
                    <div class="list-group-item p-3 <?= !$n['is_read'] ? 'bg-light' : '' ?>">
                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                            <h6 class="mb-1 fw-bold text-<?= htmlspecialchars($n['type']) ?>">
                                <i class="bi bi-info-circle me-1"></i> <?= htmlspecialchars($n['title']) ?>
                            </h6>
                            <small class="text-muted"><?= date('M d, Y h:i A', strtotime($n['created_at'])) ?></small>
                        </div>
                        <p class="mb-1 text-secondary"><?= htmlspecialchars($n['message']) ?></p>
                        <?php if (!empty($n['link'])): ?>
                            <a href="<?= BASE_URL . $n['link'] ?>" class="btn btn-sm btn-outline-primary mt-2">View Details &rarr;</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>