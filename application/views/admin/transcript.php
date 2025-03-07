
<body>
<div class="page-content">
<div class="container-fluid">
    <div class="container mt-4">
        <h2 class="mb-4">Search Student Transcript</h2>
        
        <?php echo form_open_multipart('admin/transcript', 'class="user"'); ?>
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Enter USN" name="usn" id="usn" required>
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
        
        <div id="transcript-result">
            <!-- Transcript details will be displayed here -->
            <?php if (isset($students)): ?>
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center"
                        style="background-color:#2f4050; padding: 2px 10px; font-size: 14px;">
                        <h3 class="card-title text-white mt-2">Student Details</h3>
                        <div class="card-tools d-flex">
                            <?php echo anchor('admin/generate_transcript_pdf/' . $students->id, 
                                '<span class="icon"><i class="fas fa-file-pdf"></i></span> <span class="text">Generate Transcript</span>', 
                                'class="btn btn-primary btn-sm btn-icon-split shadow-sm ml-2 generate-transcript" data-usn="' . $students->usn . '"'); 
                            ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">USN</label>
                                    <p><?= $students->usn; ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Student Name</label>
                                    <p><?= $students->student_name; ?></p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Admission Year</label><br>
                                    <p><?= $students->admission_year; ?></p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Programme</label>
                                    <p><?= $students->programme; ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Branch</label>
                                    <p><?= $students->branch; ?></p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Date of Birth</label>
                                    <p><?= $students->date_of_birth; ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Gender</label><br>
                                    <?= $students->gender; ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Category</label><br>
                                    <?= $students->category; ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Mobile</label><br>
                                    <?= $students->mobile; ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Parent Mobile</label>
                                    <p><?= $students->parent_mobile; ?></p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Father Name</label>
                                    <p><?= $students->father_name; ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Mother Name</label>
                                    <p><?= $students->mother_name; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Display marks by semester -->
                <div class="section mt-4">
                    <?php for ($semester = 1; $semester <= 8; $semester++): ?>
                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background-color:#2f4050; padding: 2px 10px; font-size: 14px;">
                            <h4 class="card-title text-white mt-2">Semester <?= $semester; ?></h4>
                            <div class="card-tools d-flex">
                                <button class="btn btn-link text-white" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#semester-<?= $semester; ?>" aria-expanded="false"
                                    aria-controls="semester-<?= $semester; ?>">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>

                        <div id="semester-<?= $semester; ?>" class="collapse">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
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
                                        <?php if (!empty($studentmarks[$semester])): ?>
                                        <?php $sno = 1; ?>
                                        <?php foreach ($studentmarks[$semester] as $course): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td><?= $course->course_code; ?></td>
                                            <td><?= $course->course_name; ?></td>
                                            <td><?= $course->credits_earned; ?></td>
                                            <td><?= $course->cie; ?></td>
                                            <td><?= $course->see; ?></td>
                                            <td><?= $course->grade; ?></td>
                                            <td><?= $course->grade_points; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No courses found for this semester.</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
        </div>
    </div>
</body>
