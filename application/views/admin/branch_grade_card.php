<!-- Content Wrapper. Contains page content -->
<div class="page-content">
    <div class="container-fluid">


        <!-- <div class="card card-info shadow"> -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18"><?= $page_title; ?></h4>
                    <?php echo anchor('admin/add_newstudent', '<span class="icon"><i class="fas fa-plus"></i></span><span class="text"> Add  Student</span>', 'class="btn btn-danger btn-sm"'); ?>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if ($this->session->flashdata('message')): ?>
                    <div class="alert <?php echo $this->session->flashdata('status'); ?> mt-3">
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                <?php endif; ?>

                <?php echo form_open_multipart($action, 'class="user" id="enquiry_list"'); ?>
                <div class="row">
                    <!-- Admission Year Filter -->


                    <!-- Programme Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="programme">Programme </label>
                            <?php
                            echo form_dropdown('programme', $programme_options, (set_value('programme')) ? set_value('programme') : 'programme', 'class="form-control " id="programme"');
                            ?>
                            <span class="text-danger"><?php echo form_error('programme'); ?></span>
                        </div>
                    </div>

                    <!-- Branch Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="branch">Branch </label>

                            <?php
                            echo form_dropdown('branch', $branch_options, (set_value('branch')) ? set_value('branch') : 'branch', 'class="form-control " id="branch"');
                            ?>
                            <span class="text-danger"><?php echo form_error('branch'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">

                        <div class="form-group">
                            <label for="admission_year">Result Year <span class="text-danger">*</span></label>
                            <select name="result_year" id="result_year" class="form-control ">
                                 <option >
                                       Select Result Year
                                    </option>
                                <?php foreach ($result_year_options as $option): ?>
                                    <option value="<?php echo $option['value']; ?>">
                                        <?php echo $option['label']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('result_year'); ?></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-primary btn-block" name="Update"
                            id="Update">Download</button>
                    </div>
                </div>
                </form>


            </div>
        </div>


        <!-- </div>
        </div> -->
    </div>
</div>
<!-- /.content-wrapper -->

<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this student?
            </div>
            <div class="modal-footer">
                <a href="<?= base_url('admin/students'); ?>" class="btn btn-secondary">Cancel</a>
                <a id="deleteConfirmButton" href="#" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Function to open the delete modal and set the action URL
    function openDeleteModal(deleteUrl) {
        // Set the href of the confirm button to the delete URL
        document.getElementById('deleteConfirmButton').setAttribute('href', deleteUrl);

        // Show the modal
        $('#deleteModal').modal('show');
    }
</script>