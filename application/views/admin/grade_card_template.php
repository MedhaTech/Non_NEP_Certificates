<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center"
        style="background-color:#2f4050; padding: 2px 10px; font-size: 14px;">
        <h3 class="card-title text-white mt-2">
            <?php if ($is_supplementary): ?>
                Supplementary Attempt <?= $sequence ?>
                <?php 
                $semesters = explode(',', $semester);
                $semesterText = count($semesters) > 1 ? 
                    'Semesters ' . implode(', ', $semesters) : 
                    'Semester ' . $semester;
                ?>
            <?php else: ?>
                Semester <?= $semester ?> <?= $semester_type ?>
            <?php endif; ?>
        </h3>
        <div class="card-tools d-flex">
            <a href="<?= base_url('admin/generate_grade_card_pdf/' . base64_encode($student->usn) . '/' . base64_encode($semester) . '/' . base64_encode($is_supplementary ? '1' : '0') . '/' . base64_encode($is_supplementary && !empty($sequence) ? $sequence : '0')) ?>" class="btn btn-light btn-sm">
                <i class="fas fa-file-pdf"></i> Generate PDF
            </a>
        </div>
    </div>
    
    <div class="card-body" id="grade-card-content">
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h4><?= isset($student->college_name) ? $student->college_name : 'BMS College of Engineering' ?></h4>
                <h5>Grade Card</h5>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">USN</label>
                    <p><strong><?= $student->usn; ?></strong></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Student Name</label>
                    <p><strong><?= $student->student_name; ?></strong></p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">Admission Year</label>
                    <p><strong><?= $student->admission_year; ?></strong></p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">Programme</label>
                    <p><strong><?= $student->programme; ?></strong></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Branch</label>
                    <p><strong><?= $student->branch; ?></strong></p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <p><strong><?= $student->date_of_birth; ?></strong></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <p><strong><?= $student->gender; ?></strong></p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <p><strong><?= $student->category; ?></strong></p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">Mobile</label>
                    <p><strong><?= $student->mobile; ?></strong></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Parent Mobile</label>
                    <p><strong><?= $student->parent_mobile; ?></strong></p>
                </div>
            </div>
        </div>
        
        <hr>
        
        <?php if (empty($marks)): ?>
            <div class="alert alert-info">No courses found for this semester and attempt.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="bg-light">
                            <th>SNO</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credits</th>
                            <th>CIE</th>
                            <th>SEE</th>
                            <th>Grade</th>
                            <th>Grade Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sno = 1; ?>
                        <?php 
                        $totalCredits_actual = 0;
                        $totalCredits_earned = 0;
                        $totalGradePoints = 0;
                        foreach ($marks as $mark): ?>
                            <tr>
                                <td><?= $sno++ ?></td>
                                <td><?= $mark->course_code; ?></td>
                                <td><?= $mark->course_name; ?></td>
                                <td><?= $mark->credits_earned; ?></td>
                                <td><?= $mark->cie; ?></td>
                                <td><?= $mark->see; ?></td>
                                <td><?= $mark->grade; ?></td>
                                <td><?= $mark->grade_points; ?></td>
                            </tr>
                            <?php 
                            $totalCredit_earned += $mark->credits_earned;
                            $totalCredits_actual += $mark->credits_earned;
                            $totalGradePoints += ($mark->grade_points * $mark->credits_earned);
                            ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Credits Registered</th>
                            <td><?= $totalCredits_actual ?></td>
                        </tr>
                        <tr>
                            <th>Total Credits Earned</th>
                            <td><?= $totalCredit_earned ?></td>
                        </tr>
                        <tr>
                            <th>SGPA</th>
                            <td><?= isset($marks[0]->sgpa) ? $marks[0]->cgpa : 'N/A' ?></td>
                        </tr>
                        <tr>
                            <th>CGPA</th>
                            <td><?= isset($marks[0]->cgpa) ? $marks[0]->cgpa : 'N/A' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            Grade Points Table
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm m-0">
                                <tr>
                                    <th>Grade</th>
                                    <th>S</th>
                                    <th>A</th>
                                    <th>B</th>
                                    <th>C</th>
                                    <th>D</th>
                                    <th>E</th>
                                    <th>F</th>
                                </tr>
                                <tr>
                                    <td>Points</td>
                                    <td>10</td>
                                    <td>9</td>
                                    <td>8</td>
                                    <td>7</td>
                                    <td>6</td>
                                    <td>4</td>
                                    <td>0</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    // Print functionality
    $('#print-grade-card').click(function() {
        var printContents = document.getElementById('grade-card-content').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = '<div class="container">' + printContents + '</div>';
        window.print();
        document.body.innerHTML = originalContents;

        var usn = '<?= $student->usn ?>';
        var details = 'Grade Card (Sem: <?= $semester ?><?= $is_supplementary ? ", Supplementary Attempt: $sequence" : ", Regular" ?>)';

        $.ajax({
            url: "<?= site_url('admin/update_certificate_log') ?>",
            type: "POST",
            data: {
                usn: usn,
                details: details
            },
            success: function(response) {
                console.log("Certificate log updated:", response);
            },
            error: function(xhr, status, error) {
                console.error("Error updating log:", xhr.responseText);
            }
        });
    });

    // Remove spaces in USN input if present
    $('#usn').on('input', function() {
        this.value = this.value.replace(/\s+/g, '');
    });
});
</script>
