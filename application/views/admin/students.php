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

                <!-- Filter Form -->
                <?php echo form_open_multipart($action, 'class="user" id="enquiry_list"'); ?>
                <div class="row">
                    <!-- Admission Year Filter -->
                    <!-- <div class="col-md-3">
                        <div class="form-group">
                            <label for="admission_year">Admission Year <span class="text-danger">*</span></label>
                            <?php 
                    $admission_options = array("0" => "Select Admission Year", "All" => "All Admission Years") + $admission_options;
                    echo form_dropdown('admission_year', $admission_options, 
                        set_value('admission_year', $selected_admission_year), 
                        'class="form-control" id="admission_year"'); 
                ?>
                            <span class="text-danger"><?php echo form_error('admission_year'); ?></span>
                        </div>
                    </div> -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="admission_year">Admission Year <span class="text-danger">*</span></label>
                            <?php 
                                // Display only the "Select Admission Year" option without "All"
                                echo form_dropdown('admission_year', $admission_options, 
                                    set_value('admission_year', $selected_admission_year), 
                                    'class="form-control" id="admission_year"'); 
                            ?>
                            <span class="text-danger"><?php echo form_error('admission_year'); ?></span>
                        </div>
                    </div>

                    <!-- Programme Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="programme">Programme <span class="text-danger">*</span></label>
                            <?php 
                    // Adding "Select Programme" as the default value, and "All" as an option
                    $programme_options = array("0" => "Select Programmes", "All" => "All Programmes") + $programme_options;
                    echo form_dropdown('programme', $programme_options, 
                        set_value('programme', $selected_programme), 
                        'class="form-control" id="programme"'); 
                ?>
                            <span class="text-danger"><?php echo form_error('programme'); ?></span>
                        </div>
                    </div>

                    <!-- Branch Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="branch">Branch <span class="text-danger">*</span></label>
                            <?php 
                    // Adding "Select Branch" as the default value, and "All" as an option
                    $branch_options = array("0" => "Select Branches", "All" => "All Branches") + $branch_options;
                    echo form_dropdown('branch', $branch_options, 
                        set_value('branch', $selected_branch), 
                        'class="form-control" id="branch"'); 
                ?>
                            <span class="text-danger"><?php echo form_error('branch'); ?></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-primary btn-block" name="Update"
                            id="Update">Filter</button>
                    </div>
                </div>
                </form>


                <div class="row">
                    <div class="col-md-12">
                        <?php
                            if (count($students)) {
                                // Table setup
                                $table_setup = array('table_open' => '<table class="table dt-responsive nowrap table-bordered" border="1" id="basic-datatable">');
                                $this->table->set_template($table_setup);

                                // Table headings
                                $print_fields = array('S.NO', 'USN', 'Student Name', 'Admission Year', 'Programme', 'Branch', 'Action');
                                $this->table->set_heading($print_fields);

                                $i = 1;
                                foreach ($students as $student) {
                                    $edit_url = base_url('admin/editstudent/' . $student->id);  // Adjust URL if necessary
                                    $encryptId = base64_encode($student->id);

                                    $delete_url = base_url('admin/deletestudent/' . $encryptId); // Delete URL with encrypted ID

                                    // Filling table rows dynamically
                                    $result_array = array(
                                        $i++,
                                        anchor('admin/studentdetails/'.$encryptId,  $student->usn),
                                        $student->student_name,
                                        $student->admission_year,
                                        $student->programme,
                                        $student->branch,
                                        "<a href='{$edit_url}' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i> Edit</a> 
                                         <a href='#' class='btn btn-danger btn-sm' onclick='openDeleteModal(\"{$delete_url}\")'><i class='fa fa-trash'></i> Delete</a>"                                    );
                                    $this->table->add_row($result_array);
                                }
                                // Generating and displaying the table
                                echo $this->table->generate();
                            } else {
                                // No data available message
                                echo "<div class='text-center'><img src='" . base_url() . "assets/images/no_data.jpg' class='nodata'></div>";
                            }
                        ?>
                    </div><!-- end col-->
                </div><!-- end row-->
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