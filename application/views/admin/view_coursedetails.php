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
                                    <label for="inputEmail3" class="col-3 col-form-label">Course Code</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->course_code != NULL) {
                                    echo $students->course_code;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Course Name</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->course_name != NULL) {
                                    echo $students->course_name;
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
                                    <label for="inputEmail3" class="col-3 col-form-label">Semester</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->semester != NULL) {
                                    echo $students->semester;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Year</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->year != NULL) {
                                    echo $students->year;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Course Hours</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->crhrs != NULL) {
                                    echo $students->crhrs;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <label for="inputEmail3" class="col-3 col-form-label">Course Order</label>
                                    <div class="col-9 col-form-label">
                                        <?php
                                if ($students->course_order != NULL) {
                                    echo $students->course_order;
                                } else {
                                    echo "--";
                                }
                                ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <!-- Back Link -->
                                    <a href="<?php echo base_url('admin/courses/'); ?>"
                                        class="btn btn-dark mt-3">Cancel</a>
                                </div>

                            </form>
                        </div>
                
                 </div>
             </div>
    </div>
</div>