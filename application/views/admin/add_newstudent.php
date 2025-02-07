<div class="page-content">
    <!-- <section class="content-header"> -->
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
                <!-- <div class="card"> -->
                <div class="card card-body">
                    <!-- Flash Message for success or error -->
                    <?php if ($this->session->flashdata('message')): ?>
                    <div class="alert <?php echo $this->session->flashdata('status'); ?> mt-3">
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                    <?php endif; ?>
                    <!-- Page Title -->
                    <!-- <h4 class="card-title text-secondary">Add New Course</h4> -->

                    <!-- Form to Add New Student -->
                    <?php echo form_open_multipart($action, 'class="user"'); ?>

                    <div class="form-group row">
                        <label for="usn" class="col-sm-4 col-form-label">USN<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="usn" name="usn"
                                value="<?php echo (set_value('usn')) ? set_value('usn') : $usn; ?>">
                            <span class="text-danger"><?php echo form_error('usn'); ?></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="student_name" class="col-sm-4 col-form-label">Student Name<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="student_name" name="student_name"
                                value="<?php echo (set_value('student_name')) ? set_value('student_name') : $student_name; ?>">
                            <span class="text-danger"><?php echo form_error('student_name'); ?></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="admission_year" class="col-sm-4 col-form-label">Admission Year<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                        <?php 
                            echo form_dropdown('admission_year', $admission_options, (set_value('admission_year')) ? set_value('admission_year') : '', 'class="form-control" id="admission_year"'); ?>
                            <span class="text-danger"><?php echo form_error('admission_year'); ?></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="programme" class="col-sm-4 col-form-label">Programme<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <?php 
                            echo form_dropdown('programme', $programme_options, (set_value('programme')) ? set_value('programme') : '', 'class="form-control" id="programme"'); ?>
                            <span class="text-danger"><?php echo form_error('programme'); ?></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="branch" class="col-sm-4 col-form-label">Branch<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <!-- <input type="text" class="form-control" id="branch" name="branch"
                                value="<?php echo (set_value('branch')) ? set_value('branch') : $branch; ?>"> -->
                                <?php 
                                echo form_dropdown('branch', $branch_options, (set_value('branch')) ? set_value('branch') : '', 'class="form-control" id="branch"'); ?>
                            <span class="text-danger"><?php echo form_error('branch'); ?></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="date_of_birth" class="col-sm-4 col-form-label">Date of Birth<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                value="<?php echo (set_value('date_of_birth')) ? set_value('date_of_birth') : $date_of_birth; ?>">
                            <span class="text-danger"><?php echo form_error('date_of_birth'); ?></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="gender" class="col-sm-4 col-form-label">Gender<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="gender" name="gender"
                                value="<?php echo (set_value('gender')) ? set_value('gender') : $gender; ?>">
                            <span class="text-danger"><?php echo form_error('gender'); ?></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="category" class="col-sm-4 col-form-label">Category<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="category" name="category"
                                value="<?php echo (set_value('category')) ? set_value('category') : $category; ?>">
                            <span class="text-danger"><?php echo form_error('category'); ?></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="mobile" class="col-sm-4 col-form-label">Mobile<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" id="mobile" name="mobile"
                                value="<?php echo (set_value('mobile')) ? set_value('mobile') : $mobile; ?>">
                            <span class="text-danger"><?php echo form_error('mobile'); ?></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="parent_mobile" class="col-sm-4 col-form-label">Parent Mobile<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" id="parent_mobile" name="parent_mobile"
                                value="<?php echo (set_value('parent_mobile')) ? set_value('parent_mobile') : $parent_mobile; ?>">
                            <span class="text-danger"><?php echo form_error('parent_mobile'); ?></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="father_name" class="col-sm-4 col-form-label">Father Name<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="father_name" name="father_name"
                                value="<?php echo (set_value('father_name')) ? set_value('father_name') : $father_name; ?>">
                            <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="mother_name" class="col-sm-4 col-form-label">Mother Name<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mother_name" name="mother_name"
                                value="<?php echo (set_value('mother_name')) ? set_value('mother_name') : $mother_name; ?>">
                            <span class="text-danger"><?php echo form_error('mother_name'); ?></span>
                        </div>
                    </div>

                    <!-- <button type="submit" class="btn btn-primary mt-3">Add Course</button> -->
                    <div class="mb-3">
                        <!-- Add Course Button -->
                        <button type="submit" class="btn btn-danger mt-3">Add Student</button>

                        <!-- Back Link -->
                        <a href="<?php echo base_url('admin/students/'); ?>"
                            class="btn btn-dark mt-3 ml-2">Cancel</a>
                    </div>

                  </form>

                </div>
            <!-- </div> -->
        </div>
    </div>
</div>
<!-- </section> -->
</div>