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
                        <?php echo form_open('admin/editstudent/' . $admissionDetails->id, 'class="form-horizontal"'); ?>

                                <input type="hidden" class="form-control" name="id"
                                    value="<?php echo $admissionDetails->id; ?>">

                                <div class="form-group">
                                    <label for="usn">Usn<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="usn" id="usn"
                                        value="<?php echo set_value('usn', $admissionDetails->usn); ?>">
                                        <span class="text-danger"><?php echo form_error('usn'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="student_name">Student Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="student_name" id="student_name"
                                        value="<?php echo set_value('student_name', $admissionDetails->student_name); ?>">
                                        <span class="text-danger"><?php echo form_error('student_name'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="admission_year">Admission Year</label>
                                    <input type="text" class="form-control" name="admission_year" id="admission_year"
                                        value="<?php echo set_value('admission_year', $admissionDetails->admission_year); ?>">
                                        <span class="text-danger"><?php echo form_error('admission_year'); ?></span>
                                </div>

                                <div class="form-group">
                                 <label for="programme">Programme<span class="text-danger">*</span></label>
                                    <?php 
                                        $selected_programme = isset($admissionDetails->programme) ? $admissionDetails->programme : (set_value('programme') ? set_value('programme') : '');
                                        echo form_dropdown('programme', $programme_options, $selected_programme, 'class="form-control" id="programme"');
                                    ?>
                                    <span class="text-danger"><?php echo form_error('programme'); ?></span>
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
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input type="date" class="form-control" name="date_of_birth" id="date_of_birth"
                                        value="<?php echo set_value('date_of_birth', $admissionDetails->date_of_birth); ?>">
                                        <span class="text-danger"><?php echo form_error('date_of_birth'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <input type="text" class="form-control" name="gender" id="gender"
                                        value="<?php echo set_value('gender', $admissionDetails->gender); ?>">
                                        <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <input type="text" class="form-control" name="category" id="category"
                                        value="<?php echo set_value('category', $admissionDetails->category); ?>">
                                        <span class="text-danger"><?php echo form_error('category'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="mobile">Mobile</label>
                                    <input type="text" class="form-control" name="mobile" id="mobile"
                                        value="<?php echo set_value('mobile', $admissionDetails->mobile); ?>">
                                        <span class="text-danger"><?php echo form_error('mobile'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="parent_mobile">Parent Mobile</label>
                                    <input type="text" class="form-control" name="parent_mobile" id="parent_mobile"
                                        value="<?php echo set_value('parent_mobile', $admissionDetails->parent_mobile); ?>">
                                        <span class="text-danger"><?php echo form_error('parent_mobile'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="father_name">Father Name</label>
                                    <input type="text" class="form-control" name="father_name" id="father_name"
                                        value="<?php echo set_value('father_name', $admissionDetails->father_name); ?>">
                                        <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="mother_name">Mother Name</label>
                                    <input type="text" class="form-control" name="mother_name" id="mother_name"
                                        value="<?php echo set_value('mother_name', $admissionDetails->mother_name); ?>">
                                        <span class="text-danger"><?php echo form_error('mother_name'); ?></span>
                                </div>

                            <!-- Save and Back buttons -->
                            <button type="submit" class="btn btn-danger btn-square" name="Update" id="Update">Update</button>
                            <a href="<?php echo base_url('admin/students/'); ?>" class="btn btn-dark">Cancel</a>

                        <?php echo form_close(); ?>
                    </div>
                 </div>
             </div>
    </div>
</div>