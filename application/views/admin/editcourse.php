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

                        <!-- Display Flash Message -->
                        <?php if($this->session->flashdata('message')): ?>
                        <div class="alert <?php echo $this->session->flashdata('status'); ?> mt-3">
                            <?php echo $this->session->flashdata('message'); ?>
                        </div>
                    <?php endif; ?>

                        <!-- Start Form -->
                        <?php echo form_open('admin/editcourse/' . $admissionDetails->id, 'class="form-horizontal"'); ?>

                                <input type="hidden" class="form-control" name="id"
                                    value="<?php echo $admissionDetails->id; ?>">

                                <div class="form-group">
                                    <label for="course_code">Course Code<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="course_code" id="course_code"
                                        value="<?php echo set_value('course_code', $admissionDetails->course_code); ?>">
                                        <span class="text-danger"><?php echo form_error('course_code'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="course_name">Course Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="course_name" id="course_name"
                                        value="<?php echo set_value('course_name', $admissionDetails->course_name); ?>">
                                        <span class="text-danger"><?php echo form_error('course_name'); ?></span>
                                </div>

                                <div class="form-group">
                                 <label for="programme">Programme<span class="text-danger">*</span></label>
                                    <?php 
                                        $selected_programme = isset($admissionDetails->programme) ? $admissionDetails->programme : (set_value('programme') ? set_value('programme') : '');
                                        echo form_dropdown('programme', $programme_options, $selected_programme, 'class="form-control" id="programme"');
                                    ?>
                                    <span class="text-danger"><?php echo form_error('branch'); ?></span>
                                </div>

                                <div class="form-group">
                                 <label for="branch">Branch<span class="text-danger">*</span></label>
                                    <?php 
                                        $selected_branch = isset($admissionDetails->branch) ? $admissionDetails->branch : (set_value('branch') ? set_value('branch') : '');
                                        echo form_dropdown('branch', $branch_options, $selected_branch, 'class="form-control" id="branch"');
                                    ?>
                                    <span class="text-danger"><?php echo form_error('branch'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="semester">Semester<span class="text-danger">*</span></label>
                                    <?php 
                                        $selected_semester = isset($admissionDetails->semester) ? $admissionDetails->semester : (set_value('semester') ? set_value('semester') : '');
                                        echo form_dropdown('semester', $semester_options, $selected_semester, 'class="form-control" id="semester"');
                                    ?>
                                    <span class="text-danger"><?php echo form_error('semester'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="year">Year<span class="text-danger">*</span></label>
                                    <?php 
                                        $selected_year = isset($admissionDetails->year) ? $admissionDetails->year : (set_value('year') ? set_value('year') : '');
                                        echo form_dropdown('year', $year_options, $selected_year, 'class="form-control" id="year"');
                                    ?>
                                    <span class="text-danger"><?php echo form_error('year'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="crhrs">Course Hours</label>
                                    <input type="text" class="form-control" name="crhrs" id="crhrs"
                                        value="<?php echo set_value('crhrs', $admissionDetails->crhrs); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="course_order">Course Order</label>
                                    <input type="text" class="form-control" name="course_order" id="course_order"
                                        value="<?php echo set_value('course_order', $admissionDetails->course_order); ?>">
                                </div>

                            <!-- Save and Back buttons -->
                            <button type="submit" class="btn btn-danger btn-square" name="Update" id="Update">Update</button>
                            <a href="<?php echo base_url('admin/courses/'); ?>" class="btn btn-dark">Cancel</a>

                        <?php echo form_close(); ?>
                    </div>
                 </div>
             </div>
    </div>
</div>