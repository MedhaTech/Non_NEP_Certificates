<div class="page-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"
                style="background-color:#2f4050;">
                <h3 class="card-title text-white"><?= $page_title; ?></h3>
                <div class="card-tools d-flex">
                    <?php echo anchor('admin/editstudent/' . $students->id, '<span class="icon"><i class="fas fa-edit"></i></span> <span class="text">Edit</span>', 'class="btn btn-danger btn-sm btn-icon-split shadow-sm"'); ?>
                    <?php echo anchor('admin/students', '<span class="icon"><i class="fas fa-arrow-left"></i></span> <span class="text">Close</span>', 'class="btn btn-secondary btn-sm btn-icon-split shadow-sm ml-2"'); ?>
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

        <div class="section">
            <?php for ($semester = 1; $semester <= 8; $semester++): ?>
            <div class="card">
                <!-- Semester Header Section with Accordion -->
                <div class="card-header d-flex justify-content-between align-items-center"
                    style="background-color:#2f4050;">
                    <h4 class="card-title text-white">Semester <?= $semester; ?></h4>
                    <div class="card-tools d-flex">
                        <!-- Add a button to toggle the collapse (accordion) -->
                        <button class="btn btn-link text-white" type="button" data-bs-toggle="collapse"
                            data-bs-target="#semester-<?= $semester; ?>" aria-expanded="false"
                            aria-controls="semester-<?= $semester; ?>">
                            <i class="fas fa-chevron-down"></i> 
                        </button>
                    </div>
                </div>

                <!-- Semester Table Section wrapped in a collapse div -->
                <div id="semester-<?= $semester; ?>" class="collapse">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SNO</th>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>CIE</th>
                                    <th>SEE</th>
                                    <th>Grade</th>
                                    <th>Grade Points</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty(${"semester_$semester"})): ?>
                                <?php $sno = 1; ?>
                                <?php foreach (${"semester_$semester"} as $course): ?>
                                <tr>
                                    <td><?= $sno++; ?></td>
                                    <td><?= $course->course_code; ?></td>
                                    <td><?= $course->course_name; ?></td>
                                    <td><?= $course->cie; ?></td>
                                    <td><?= $course->see; ?></td>
                                    <td><?= $course->grade; ?></td>
                                    <td><?= $course->grade_points; ?></td>
                                    <!-- <td>
                                        <a href="<?= site_url('admin/edit_marks/' . $course->usn); ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                    </td> -->
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

    </div>
</div>