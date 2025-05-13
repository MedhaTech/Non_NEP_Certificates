<div class="page-content">
    <div class="container-fluid">
        <div class="card-header d-flex justify-content-between align-items-center"
            style="background-color:#2f4050; padding: 2px 10px; font-size: 14px;">
            <h3 class="card-title text-white mt-2"><?= $page_title; ?></h3>
            <div class="card-tools d-flex">

                <?php echo anchor(
                    'admin/generate_bulk_grade_pdf/' . base64_encode($students->usn),
                    '<span class="icon"><i class="fas fa-file-pdf"></i></span> <span class="text">Generate Grade Card</span>',
                    'class="btn btn-primary btn-sm btn-icon-split shadow-sm ml-2 generate-transcript" data-usn="' . $studentss->usn . '"'
                );
                ?>
                <?php echo anchor('admin/studentss', '<span class="icon"><i class="fas fa-arrow-left"></i></span> <span class="text">Close</span>', 'class="btn btn-secondary btn-sm btn-icon-split shadow-sm ml-2"'); ?>
            </div>
        </div>
        <?php
        foreach ($studentmarks as $resultDate => $subjects):

            $is_supplementary = 0;

            if (date('n', strtotime($resultDate)) == 7) {
                $is_supplementary = 1;
            }
        ?>

            <div class="card">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center"
                        style="background-color:#2f4050; padding: 2px 10px; font-size: 14px;">
                        <h3 class="card-title text-white mt-2">
                            <?php if ($is_supplementary): ?>
                                Supplementary Attempt : <?= date('F Y', strtotime($resultDate)); ?>

                            <?php else: ?>
                                Regular Attempt : <?= date('F Y', strtotime($resultDate)); ?>
                            <?php endif; ?>
                        </h3>
                        <div class="card-tools d-flex">
                            <a href="<?= base_url('admin/generate_grade_card_pdf_details/' . base64_encode($students->usn) . '/' . base64_encode($resultDate) . '/' . base64_encode($is_supplementary ? '1' : '0')) ?>" class="btn btn-light btn-sm">
                                <i class="fas fa-file-pdf"></i> Generate PDF
                            </a>
                        </div>
                    </div>

                    <div class="card-body" id="grade-card-content">
                        <div class="row">
                            <div class="col-md-12 text-center mb-4">
                                <h4><?= isset($students->college_name) ? $students->college_name : 'BMS College of Engineering' ?></h4>
                                <h5>Grade Card</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">USN</label>
                                    <p><strong><?= $students->usn; ?></strong></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">students Name</label>
                                    <p><strong><?= $students->student_name; ?></strong></p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Admission Year</label>
                                    <p><strong><?= $students->admission_year; ?></strong></p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Programme</label>
                                    <p><strong><?= $students->programme; ?></strong></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Branch</label>
                                    <p><strong><?= $students->branch; ?></strong></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Date of Birth</label>
                                    <p><strong><?= $students->date_of_birth; ?></strong></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Gender</label>
                                    <p><strong><?= $students->gender; ?></strong></p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Category</label>
                                    <p><strong><?= $students->category; ?></strong></p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Mobile</label>
                                    <p><strong><?= $students->mobile; ?></strong></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Parent Mobile</label>
                                    <p><strong><?= $students->parent_mobile; ?></strong></p>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <?php if (empty($subjects)): ?>
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
                                        foreach ($subjects as $mark): ?>
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
                                            <td><?= isset($mark->sgpa) ? $mark->cgpa : 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <th>CGPA</th>
                                            <td><?= isset($mark->cgpa) ? $mark->cgpa : 'N/A' ?></td>
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
            </div>
        <?php endforeach; ?>