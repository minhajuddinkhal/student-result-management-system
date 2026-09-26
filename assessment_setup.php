<?php
$page_title = 'Assessment Marks Distribution Setup';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary">
            <i class="bi bi-sliders me-2"></i>Assessment Structure for <?= htmlspecialchars($assignment['course_code']) ?> - <?= htmlspecialchars($assignment['course_title']) ?>
        </h6>
        <small class="text-muted">Faculty can distribute total marks between Mid Term and Final Term (e.g. 40% Mid / 60% Final) and create dynamic sub-components (Quizzes, Vivas, Attendance, Assignments, Written Exams).</small>
    </div>

    <div class="card-body p-4">
        <form action="<?= BASE_URL ?>/teacher/assessment-setup?assignment_id=<?= $assignment['assignment_id'] ?>" method="POST">
            <!-- Term Split Weightages -->
            <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark">Midterm Weightage (%)</label>
                    <div class="input-group">
                        <input type="number" step="0.5" name="mid_weight" id="mid_weight" class="form-control" value="<?= $assignment['mid_weight'] ?>" min="10" max="90" required onchange="calculateTotalWeights()">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-muted">Faculty choice (e.g. 40%)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark">Final Term Weightage (%)</label>
                    <div class="input-group">
                        <input type="number" step="0.5" name="final_weight" id="final_weight" class="form-control" value="<?= $assignment['final_weight'] ?>" min="10" max="90" required onchange="calculateTotalWeights()">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-muted">Faculty choice (e.g. 60%)</small>
                </div>
            </div>

            <!-- Dynamic Sub-components: Midterm -->
            <div class="card border mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <span class="fw-bold text-primary"><i class="bi bi-card-checklist me-1"></i> Midterm Sub-components (Quizzes, Attendance, Mid Exam, etc.)</span>
                    <div>
                        <span class="me-2 small text-muted">Weight Sum: <span id="mid-sum-display" class="badge bg-secondary">0%</span></span>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAssessmentRow('mid')">
                            <i class="bi bi-plus-circle me-1"></i> Add Mid Component
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45%;">Component Name</th>
                                    <th style="width: 25%;">Max Marks</th>
                                    <th style="width: 20%;">Weightage (%)</th>
                                    <th style="width: 10%;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="mid-components-body">
                                <?php 
                                $has_mid = false;
                                foreach ($components as $c): 
                                    if ($c['term_type'] === 'mid'):
                                        $has_mid = true;
                                ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="comp_term[]" value="mid">
                                            <input type="text" name="comp_name[]" class="form-control form-control-sm" value="<?= htmlspecialchars($c['component_name']) ?>" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.5" name="comp_max[]" class="form-control form-control-sm comp-max-input" value="<?= $c['max_marks'] ?>" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.5" name="comp_weight[]" class="form-control form-control-sm comp-weight-input" value="<?= $c['weightage'] ?>" required onchange="calculateTotalWeights()">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove(); calculateTotalWeights();">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Dynamic Sub-components: Final Term -->
            <div class="card border mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <span class="fw-bold text-success"><i class="bi bi-card-checklist me-1"></i> Final Term Sub-components (Assignments, Viva, Final Written Exam, etc.)</span>
                    <div>
                        <span class="me-2 small text-muted">Weight Sum: <span id="final-sum-display" class="badge bg-secondary">0%</span></span>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="addAssessmentRow('final')">
                            <i class="bi bi-plus-circle me-1"></i> Add Final Component
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45%;">Component Name</th>
                                    <th style="width: 25%;">Max Marks</th>
                                    <th style="width: 20%;">Weightage (%)</th>
                                    <th style="width: 10%;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="final-components-body">
                                <?php 
                                $has_final = false;
                                foreach ($components as $c): 
                                    if ($c['term_type'] === 'final'):
                                        $has_final = true;
                                ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="comp_term[]" value="final">
                                            <input type="text" name="comp_name[]" class="form-control form-control-sm" value="<?= htmlspecialchars($c['component_name']) ?>" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.5" name="comp_max[]" class="form-control form-control-sm comp-max-input" value="<?= $c['max_marks'] ?>" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.5" name="comp_weight[]" class="form-control form-control-sm comp-weight-input" value="<?= $c['weightage'] ?>" required onchange="calculateTotalWeights()">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove(); calculateTotalWeights();">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Grand Summary & Save Button -->
            <div id="grand-sum-display" class="alert alert-info py-2 mb-3">
                Total Combined Weightage: 100%
            </div>

            <div class="d-flex justify-content-between">
                <a href="<?= BASE_URL ?>/teacher/courses" class="btn btn-light">Back to Courses</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Save Assessment Structure
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!$has_mid): ?>
        addAssessmentRow('mid');
        addAssessmentRow('mid');
    <?php endif; ?>
    <?php if (!$has_final): ?>
        addAssessmentRow('final');
        addAssessmentRow('final');
    <?php endif; ?>
    calculateTotalWeights();
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>