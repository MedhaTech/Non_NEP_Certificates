<div class="page-content">
    <section class="content-header">
        <div class="container-fluid">
            <div class="col-md-7 col-12 m-2">
                <div class="card">
                    <div class="card-body">
                        <!-- Flash Message for success or error -->
                        <?php if ($this->session->flashdata('message')): ?>
                            <div class="alert <?php echo $this->session->flashdata('status'); ?> mt-3">
                                    <?php echo $this->session->flashdata('message'); ?>
                            </div>
                        <?php endif; ?>
                        <!-- Page Title -->
                        <h4 class="card-title text-secondary">Add New Course</h4>
                        
                        <!-- Form to Add New Student -->
                        <?php echo form_open_multipart($action, 'class="user"'); ?>

                            <div class="form-group row">
                                <label for="course_code" class="col-sm-4 col-form-label">Course Code</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="course_code" name="course_code" value="<?php echo (set_value('course_code')) ? set_value('course_code') : $course_code; ?>">
                                    <span class="text-danger"><?php echo form_error('course_code'); ?></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="course_name" class="col-sm-4 col-form-label">Course Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="course_name" name="course_name" value="<?php echo (set_value('course_name')) ? set_value('course_name') : $course_name; ?>">
                                    <span class="text-danger"><?php echo form_error('course_name'); ?></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="branch" class="col-sm-4 col-form-label">Branch</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="branch" name="branch" value="<?php echo (set_value('branch')) ? set_value('branch') : $branch; ?>">
                                    <span class="text-danger"><?php echo form_error('branch'); ?></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="semester" class="col-sm-4 col-form-label">Semester</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="semester" name="semester" value="<?php echo (set_value('semester')) ? set_value('semester') : $semester; ?>">
                                    <span class="text-danger"><?php echo form_error('semester'); ?></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="year" class="col-sm-4 col-form-label">Year</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="year" name="year" value="<?php echo (set_value('year')) ? set_value('year') : $year; ?>">
                                    <span class="text-danger"><?php echo form_error('year'); ?></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="crhrs" class="col-sm-4 col-form-label">Course houre</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="crhrs" name="crhrs" value="<?php echo (set_value('crhrs')) ? set_value('crhrs') : $crhrs; ?>">
                                    <span class="text-danger"><?php echo form_error('crhrs'); ?></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="course_order" class="col-sm-4 col-form-label">Course Order</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="course_order" name="course_order" value="<?php echo (set_value('course_order')) ? set_value('course_order') : $course_order; ?>">
                                    <span class="text-danger"><?php echo form_error('course_order'); ?></span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Submit</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>