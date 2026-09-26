<?php
$page_title = 'Faculty Profile & Student Contact Information';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-person-lines-fill me-2"></i>Faculty Profile & Contact Details</h6>
                <small class="text-muted">Your email and office room will appear on student dashboards so they can communicate with you regarding courses.</small>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/teacher/profile" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($teacher_info['name']) ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Teacher Code</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($teacher_info['teacher_code']) ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Department</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($teacher_info['dept_name']) ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Designation</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($teacher_info['designation']) ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Public Email (Visible to Students)</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($teacher_info['email']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">11-digit Phone (013-019)</label>
                            <input type="text" name="phone" class="form-control bd-phone-input" value="<?= htmlspecialchars($teacher_info['phone']) ?>" maxlength="11" required>
                            <div class="phone-feedback small text-muted"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Office Room / Consultation Hours</label>
                        <input type="text" name="office_room" class="form-control" value="<?= htmlspecialchars($teacher_info['office_room']) ?>" placeholder="e.g. Room 402, Building A | Sun/Tue 2:00PM - 4:00PM">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Bio / Research Focus</label>
                        <textarea name="bio" class="form-control" rows="3"><?= htmlspecialchars($teacher_info['bio']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check2-circle me-1"></i> Update Contact Info
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>