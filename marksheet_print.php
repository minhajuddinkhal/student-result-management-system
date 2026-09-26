<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Academic Transcript - <?= htmlspecialchars($student_info['name']) ?> (<?= htmlspecialchars($student_info['roll_no']) ?>)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/Static/css/print.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
        }
        .sheet {
            background: #fff;
            max-width: 900px;
            margin: 20px auto;
            padding: 40px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .header-title {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-title h2 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header-title h4 {
            font-size: 16px;
            margin: 5px 0 0;
            font-style: italic;
        }
        .student-meta td {
            padding: 4px 8px;
            font-size: 13px;
        }
        .table-transcript th, .table-transcript td {
            border: 1px solid #333;
            padding: 5px 8px;
            font-size: 12px;
        }
        .grade-scale-box td, .grade-scale-box th {
            border: 1px solid #777;
            padding: 2px 4px;
            font-size: 9px;
        }
    </style>
</head>
<body>

<div class="sheet print-container">
    <div class="no-print d-flex justify-content-between align-items-center mb-3">
        <a href="<?= BASE_URL ?>/student/dashboard" class="btn btn-sm btn-secondary">&larr; Back to Portal</a>
        <button onclick="window.print()" class="btn btn-sm btn-primary"><i class="bi bi-printer"></i> Print Transcript / Save PDF</button>
    </div>

    <!-- Official Header -->
    <div class="header-title">
        <h2>STATE UNIVERSITY OF SCIENCE & TECHNOLOGY</h2>
        <h4>OFFICIAL TRANSCRIPT OF ACADEMIC RECORD</h4>
        <small class="text-muted">Office of the Controller of Examinations</small>
    </div>

    <!-- Student Metadata -->
    <div class="row mb-3">
        <div class="col-8">
            <table class="student-meta w-100">
                <tr><td style="width: 140px;"><strong>Student Name:</strong></td><td><?= htmlspecialchars($student_info['name']) ?></td></tr>
                <tr><td><strong>Roll Number:</strong></td><td><?= htmlspecialchars($student_info['roll_no']) ?></td></tr>
                <tr><td><strong>Registration No:</strong></td><td><?= htmlspecialchars($student_info['reg_no']) ?></td></tr>
                <tr><td><strong>Department:</strong></td><td><?= htmlspecialchars($student_info['dept_name']) ?> (<?= htmlspecialchars($student_info['dept_code']) ?>)</td></tr>
                <tr><td><strong>Program:</strong></td><td>Bachelor of Science (B.Sc.) - 4 Years (8 Semesters)</td></tr>
            </table>
        </div>
        <div class="col-4">
            <!-- Mini Grading System Reference -->
            <table class="grade-scale-box text-center w-100">
                <thead>
                    <tr class="table-light"><th colspan="3">GRADING SCALE</th></tr>
                    <tr><th>Marks</th><th>Grade</th><th>Point</th></tr>
                </thead>
                <tbody>
                    <tr><td>80%+</td><td>A+</td><td>4.00</td></tr>
                    <tr><td>75-79%</td><td>A</td><td>3.75</td></tr>
                    <tr><td>70-74%</td><td>A-</td><td>3.50</td></tr>
                    <tr><td>65-69%</td><td>B+</td><td>3.25</td></tr>
                    <tr><td>60-64%</td><td>B</td><td>3.00</td></tr>
                    <tr><td>55-59%</td><td>B-</td><td>2.75</td></tr>
                    <tr><td>50-54%</td><td>C+</td><td>2.50</td></tr>
                    <tr><td>45-49%</td><td>C</td><td>2.25</td></tr>
                    <tr><td>40-44%</td><td>D</td><td>2.00</td></tr>
                    <tr><td>&lt;40%</td><td>F</td><td>0.00</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Semester Course Records -->
    <?php for ($s = 1; $s <= 8; $s++): 
        if (!isset($transcript['semesters'][$s])) continue;
        $sdata = $transcript['semesters'][$s];
    ?>
        <div class="mb-3">
            <h6 class="fw-bold text-uppercase border-bottom pb-1 mb-1" style="font-size: 13px;">
                Semester <?= $s ?> (Year <?= ceil($s/2) ?>, Term <?= ($s % 2 == 1) ? 1 : 2 ?>)
            </h6>
            <table class="table-transcript w-100 text-center mb-2">
                <thead>
                    <tr class="table-light">
                        <th style="width: 15%;">Course Code</th>
                        <th style="width: 45%; text-align: left;">Course Title</th>
                        <th style="width: 10%;">Credits</th>
                        <th style="width: 10%;">Marks</th>
                        <th style="width: 10%;">Grade</th>
                        <th style="width: 10%;">Grade Point</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sdata['courses'] as $sc): ?>
                        <tr>
                            <td><?= htmlspecialchars($sc['course_code']) ?></td>
                            <td style="text-align: left;"><?= htmlspecialchars($sc['course_title']) ?></td>
                            <td><?= number_format($sc['credit_hours'], 2) ?></td>
                            <td><?= number_format($sc['grand_total'], 1) ?></td>
                            <td><strong><?= $sc['letter_grade'] ?></strong></td>
                            <td><?= number_format($sc['grade_point'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-light fw-bold">
                        <td colspan="2" style="text-align: right;">Semester Total:</td>
                        <td><?= number_format($sdata['semester_credits'], 2) ?></td>
                        <td colspan="2" style="text-align: right;">Semester GPA (SGPA):</td>
                        <td><?= number_format($sdata['sgpa'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endfor; ?>

    <!-- Cumulative Transcript Summary Box -->
    <div class="border p-2 my-3 bg-light">
        <table class="w-100 text-center" style="font-size: 13px;">
            <tr>
                <td><strong>Total Credits Attempted:</strong> <?= number_format($transcript['total_credits_attempted'], 2) ?></td>
                <td><strong>Total Credits Earned:</strong> <?= number_format($transcript['total_credits_earned'], 2) ?></td>
                <td><strong>Cumulative CGPA:</strong> <span class="fs-6 fw-bold"><?= number_format($transcript['cgpa'], 2) ?> / 4.00</span></td>
                <td><strong>Remarks:</strong> <?= htmlspecialchars($transcript['overall_grade_info']['remark'] ?? 'Good') ?></td>
            </tr>
        </table>
    </div>

    <!-- Official Signature Area -->
    <div class="signature-area d-flex justify-content-between mt-5 pt-4">
        <div class="sig-line">Prepared By</div>
        <div class="sig-line">Verified By</div>
        <div class="sig-line">Controller of Examinations</div>
    </div>
</div>

</body>
</html>