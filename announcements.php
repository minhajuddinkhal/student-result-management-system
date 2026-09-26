<?php
$page_title = 'Announcements & Notices';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-megaphone-fill me-2"></i>Post New Announcement</h6>
            </div>
            <div class="card-body p-3">
                <form action="<?= BASE_URL ?>/admin/announcements" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notice Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Semester Final Routine Published" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Audience</label>
                        <select name="target_role" class="form-select">
                            <option value="all">Everyone (Teachers & Students)</option>
                            <option value="teacher">Teachers Only</option>
                            <option value="student">Students Only</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Content</label>
                        <textarea name="content" class="form-control" rows="4" placeholder="Enter notice details..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send-fill me-1"></i> Broadcast Announcement
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Active Announcements</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($announcements)): ?>
                        <div class="text-center py-5 text-muted">No announcements posted.</div>
                    <?php else: ?>
                        <?php foreach ($announcements as $a): ?>
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 text-primary"><?= htmlspecialchars($a['title']) ?></h6>
                                    <span class="badge bg-secondary"><?= ucfirst($a['target_role']) ?></span>
                                </div>
                                <p class="mb-2 text-secondary"><?= nl2br(htmlspecialchars($a['content'])) ?></p>
                                <div class="d-flex justify-content-between align-items-center small text-muted">
                                    <span><?= date('M d, Y h:i A', strtotime($a['created_at'])) ?></span>
                                    <form action="<?= BASE_URL ?>/admin/announcements" method="POST" onsubmit="return confirm('Delete announcement?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="announcement_id" value="<?= $a['announcement_id'] ?>">
                                        <button type="submit" class="btn btn-xs btn-outline-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>