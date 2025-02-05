<div class="page-content">
    <div class="container-fluid">
              <div class="row">
                 <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 font-size-18"><?= $page_title; ?></h4>
                    </div>
                 </div>
              </div>
              <div class="row">
                <div class="col-md-7 col-12">
                        <div class="card card-body">
                            <form class="form-horizontal">
                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Usn</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->usn != NULL) {
                                    echo $students->usn;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Student Name</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->student_name != NULL) {
                                    echo $students->student_name;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Admission Year</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->admission_year != NULL) {
                                    echo $students->admission_year;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Programme</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->programme != NULL) {
                                    echo $students->programme;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Branch</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->branch != NULL) {
                                    echo $students->branch;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Date of Birth</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->date_of_birth != NULL) {
                                    echo $students->date_of_birth;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Gender</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->gender != NULL) {
                                    echo $students->gender;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Category</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->category != NULL) {
                                    echo $students->category;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Mobile</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->mobile != NULL) {
                                    echo $students->mobile;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Parent Mobile</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->parent_mobile != NULL) {
                                    echo $students->parent_mobile;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Father Name</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->father_name != NULL) {
                                    echo $students->father_name;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Mother Name</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->mother_name != NULL) {
                                    echo $students->mother_name;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <!-- Back Link -->
                                    <a href="<?php echo base_url('admin/students/'); ?>"
                                        class="btn btn-dark mt-3">Cancel</a>
                                </div>

                            </form>
                        </div>
                
                 </div>
             </div>
    </div>
</div>