<div class="main-content">
    <header class="top-navbar no-print">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h5 class="m-0 fw-bold text-secondary"><?= $page_title ?? 'Dashboard' ?></h5>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <a href="<?= BASE_URL ?>/notifications" class="btn btn-light position-relative p-2 rounded-circle" title="Notifications">
                <i class="bi bi-bell-fill text-muted"></i>
            </a>

            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">
                        <?= strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="text-start d-none d-md-block">
                        <small class="d-block fw-bold line-height-1"><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></small>
                        <small class="text-muted text-capitalize" style="font-size: 0.75rem;"><?= htmlspecialchars($_SESSION['role'] ?? '') ?></small>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/change-password"><i class="bi bi-key me-2"></i> Change Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <main class="content-body">
        <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= $flash['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>