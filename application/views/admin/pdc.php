

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18"><?= $page_title; ?></h4>
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

                <?php echo form_open('admin/pdc', 'class="user" id="pdc_search"'); ?>
                <div class="row">
                    <!-- USN Search Field -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="usn">Enter USN <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="usn" name="usn" placeholder="Enter USN" value="<?= set_value('usn'); ?>" required>
                            <span class="text-danger"><?php echo form_error('usn'); ?></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-primary btn-block" name="Search" id="Search">Search</button>
                    </div>
                </div>
                <?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger">
        <?= $this->session->flashdata('error'); ?>
    </div>
<?php endif; ?>

                </form>

                <div class="row">
                    <div class="col-md-12">
                        <?php if (isset($students)): ?>
                            <?php
                                // Table setup
                                $table_setup = array('table_open' => '<table class="table dt-responsive nowrap table-bordered" border="1">');
                                $this->table->set_template($table_setup);

                                // Table headings
                                $print_fields = array('USN', 'Student Name', 'CGPA', 'Action');
                                $this->table->set_heading($print_fields);

                                $semester_data = $this->admin_model->getStudentMarksBySemester($students->usn, 8);
                                $cgpa = !empty($semester_data) ? number_format($semester_data[0]->cgpa ?? 0, 2) : 'N/A';
                                
                                // Create PDF download URL
                                $pdf_url = base_url('admin/generate_pdc_pdf/' . base64_encode($students->id));

                                // Filling table row
                                $result_array = array(
                                    $students->usn,
                                    $students->student_name,
                                    $cgpa,
                                    "<a href='{$pdf_url}' class='btn btn-primary btn-sm'><i class='fa fa-download'></i> Download PDC</a>"
                                );
                                $this->table->add_row($result_array);
                                
                                // Generating and displaying the table
                                echo $this->table->generate();
                            ?>
                        <?php elseif (validation_errors()): ?>
                            <div class="alert alert-danger mt-4">
                                <?= validation_errors(); ?>
                            </div>
                        <?php elseif (isset($_POST['usn'])): ?>
                            <div class="text-center mt-4">
                                <img src="<?= base_url(); ?>assets/images/no_data.jpg" class="nodata"><br>
                                No data found
                            </div>
                        <?php endif; ?>
                    </div><!-- end col-->
                </div><!-- end row-->
            </div>
        </div>
    </div>
</div>
<!-- /.content-wrapper -->
