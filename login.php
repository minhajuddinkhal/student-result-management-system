<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/Static/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .login-card {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        .login-header {
            background: #f8fafc;
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .login-body {
            padding: 2rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="d-inline-flex p-3 bg-primary bg-opacity-10 text-primary rounded-circle mb-3">
            <i class="bi bi-mortarboard-fill fs-1"></i>
        </div>
        <h4 class="fw-bold text-dark m-0">University Portal</h4>
        <p class="text-muted small m-0 mt-1">Student Result Management System (SRMS)</p>
    </div>

    <div class="login-body">
        <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show py-2" role="alert">
                <?= $flash['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/login-action" method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Username or Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Enter your username or email" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </button>
        </form>

        <!-- Demo Accounts Quick Fill Accordion -->
        <div class="card bg-light border-0 mt-3">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-secondary small"><i class="bi bi-info-circle me-1"></i> Quick Demo Accounts:</span>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 text-xs" onclick="fillCredentials('admin', 'admin123')">
                        <i class="bi bi-shield-lock"></i> Admin
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-success py-1 px-2 text-xs" onclick="fillCredentials('prof_john', 'teacher123')">
                        <i class="bi bi-person-badge"></i> Teacher
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-info py-1 px-2 text-xs text-dark" onclick="fillCredentials('student_ashik', 'student123')">
                        <i class="bi bi-mortarboard"></i> Student
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillCredentials(u, p) {
    document.querySelector('input[name="username"]').value = u;
    document.querySelector('input[name="password"]').value = p;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>