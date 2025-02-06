<div class="page-content">
    <div class="container-fluid">
        <!-- <div class="row">
                 <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 font-size-18"><?= $page_title; ?></h4>
                    </div>
                 </div>
              </div> -->
        <div class="card">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">STUDENT DETAILS</h5>
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
                <div class="mb-3">
                    <!-- Back Link -->
                    <a href="<?php echo base_url('admin/students/'); ?>"
                        class="btn btn-dark mt-3">Cancel</a>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
</div>