<?php
$page_title = 'Course Retake Appeals (< C Grade)';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="row g-4">
    <!-- Submit New Appeal -->
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-danger"><i class="bi bi-arrow-repeat me-2"></i>Apply for Course Retake</h6>
                <small class="text-muted">University Rule: If you received a grade less than 'C' (&lt; 2.25 Grade Point / &lt; 45%), you can appeal to retake the course. When Administrator approves, it will be automatically assigned to your dashboard.</small>
            </div>
            <div class="card-body p-3">
                <?php if (empty($eligible_courses)): ?>
                    <div class="alert alert-success py-3 text-center">
                        <i class="bi bi-check-circle-fill fs-3 d-block mb-2 text-success"></i>
                        <strong>No Eligible Retake Courses!</strong>
                        <p class="small text-muted mb-0 mt-1">You do not have any published course results with a grade below 'C'. Keep up the great work!</p>
                    </div>
                <?php else: ?>
                    <form action="<?= BASE_URL ?>/student/retake-appeal" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Course Eligible for Retake</label>
                            <select name="course_select" id="course_select" class="form-select" required onchange="updateCourseSelection(this)">
                                <option value="">-- Choose Course with Grade &lt; C --</option>
                                <?php foreach ($eligible_courses as $ec): ?>
                                    <option value="<?= $ec['course_id'] ?>" 
                                            data-resultid="<?= $ec['result_id'] ?>"
                                            data-grade="<?= $ec['letter_grade'] ?>"
                                            data-gp="<?= $ec['grade_point'] ?>">
                                        <?= htmlspecialchars($ec['course_code']) ?> - <?= htmlspecialchars($ec['course_title']) ?> (Grade: <?= $ec['letter_grade'] ?> / <?= $ec['grade_point'] ?> GP)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="course_id" id="hidden_course_id">
                            <input type="hidden" name="previous_result_id" id="hidden_result_id">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason / Justification for Retake</label>
                            <textarea name="reason" class="form-control" rows="4" placeholder="Explain why you wish to retake this course and your improvement plan..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-send-fill me-1"></i> Submit Retake Appeal to Admin
                        </button>
                    </form>

                    <script>
                    function updateCourseSelection(selectElem) {
                        const opt = selectElem.options[selectElem.selectedIndex];
                        document.getElementById('hidden_course_id').value = opt.value;
                        document.getElementById('hidden_result_id').value = opt.getAttribute('data-resultid');
                    }
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Appeal History -->
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>My Retake Appeals History</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Appeal #</th>
                                <th>Course</th>
                                <th>Prev Grade</th>
                                <th>Status</th>
                                <th>Admin Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($appeals)): ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">No retake appeals submitted yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($appeals as $a): ?>
                                    <tr>
                                        <td>#<?= $a['appeal_id'] ?></td>
                                        <td>
                                            <span class="fw-bold d-block"><?= htmlspecialchars($a['course_code']) ?></span>
                                            <small class="text-muted"><?= htmlspecialchars($a['course_title']) ?></small>
                                        </td>
                                        <td><span class="badge bg-danger"><?= $a['previous_grade'] ?> (<?= $a['previous_grade_point'] ?>)</span></td>
                                        <td>
                                            <?php if ($a['status'] === 'pending'): ?>
                                                <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Under Admin Review</span>
                                            <?php elseif ($a['status'] === 'approved'): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Approved & Assigned</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small class="text-muted"><?= htmlspecialchars($a['admin_remarks'] ?? 'Pending') ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>