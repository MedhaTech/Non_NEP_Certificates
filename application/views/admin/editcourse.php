<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="col-md-12 col-12 m-2">
                <div class="card">
                <div class="card-body">
    <h4 class="card-title text-secondary">Course Details</h4>

    <!-- Display Flash Message -->
    <?php if($this->session->flashdata('message')): ?>
        <div class="alert <?php echo $this->session->flashdata('status'); ?> mt-3">
            <?php echo $this->session->flashdata('message'); ?>
        </div>
    <?php endif; ?>

    <!-- Start Form -->
    <?php echo form_open('admin/editcourse/' . $admissionDetails->id, 'class="form-horizontal"'); ?>

    <div class="row">
        <div class="col-md-6">
            <input type="hidden" class="form-control" name="id" value="<?php echo $admissionDetails->id; ?>">

            <div class="form-group">
                <label for="course_code">Course Code</label>
                <input type="text" class="form-control" name="course_code" id="course_code"
                    value="<?php echo set_value('course_code', $admissionDetails->course_code); ?>" >
            </div>

            <div class="form-group">
                <label for="course_name">Course Name</label>
                <input type="text" class="form-control" name="course_name" id="course_name"
                    value="<?php echo set_value('course_name', $admissionDetails->course_name); ?>" >
            </div>

            <div class="form-group">
                <label for="branch">Branch</label>
                <input type="text" class="form-control" name="branch" id="branch"
                    value="<?php echo set_value('branch', $admissionDetails->branch); ?>" >
            </div>

            <div class="form-group">
                <label for="semester">Semester</label>
                <input type="text" class="form-control" name="semester" id="semester"
                    value="<?php echo set_value('semester', $admissionDetails->semester); ?>" >
            </div>

            <div class="form-group">
                <label for="year">Year</label>
                <input type="text" class="form-control" name="year" id="year"
                    value="<?php echo set_value('year', $admissionDetails->year); ?>" >
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="crhrs">Course Hours</label>
                <input type="text" class="form-control" name="crhrs" id="crhrs"
                    value="<?php echo set_value('crhrs', $admissionDetails->crhrs); ?>" >
            </div>

            <div class="form-group">
                <label for="course_order">Course Order</label>
                <input type="text" class="form-control" name="course_order" id="course_order"
                    value="<?php echo set_value('course_order', $admissionDetails->course_order); ?>" >
            </div>
        </div>
    </div>

    <!-- Save and Back buttons -->
    <button type="submit" class="btn btn-info btn-square" name="Update" id="Update">SAVE</button>
    <a href="<?php echo base_url('admin/courses/'); ?>" class="btn btn-secondary">Back</a>

    <?php echo form_close(); ?>
</div>

                </div>
            </div>
        </div>
    </section>
</div>