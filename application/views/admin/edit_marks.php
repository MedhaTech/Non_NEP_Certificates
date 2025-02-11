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

                    <?php echo form_open('admin/edit_marks/' . $course->usn, 'class="form-horizontal"'); ?>

                    <div class="form-group">
                        <label for="cie">CIE<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="cie" id="cie"
                            value="<?php echo set_value('cie', $course->cie); ?>">
                            <span class="text-danger"><?php echo form_error('cie'); ?></span>
                    </div>

                    <div class="form-group">
                        <label for="see">SEE</label>
                        <input type="number" name="see" id="see" class="form-control" value="<?= $course->see; ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="grade">Grade</label>
                        <input type="text" name="grade" id="grade" class="form-control" value="<?= $course->grade; ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="grade_points">Grade Points</label>
                        <input type="number" name="grade_points" id="grade_points" class="form-control"
                            value="<?= $course->grade_points; ?>" required>
                    </div>
                    <div class="form-group">
                    <button type="submit" class="btn btn-danger btn-square" name="Update" id="Update">Update</button>
                    <a href="<?php echo base_url('admin/students/'); ?>" class="btn btn-dark">Cancel</a>
                    </div>

                    <?php echo form_close(); ?>

                </div>
            </div>
        </div>
    </div>
</div>