<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18"><?= $page_title; ?></h4>
                </div>
            </div>
        </div>

        <!-- Card Start -->
        <div class="card">
            <div class="card-body">
                <!-- Flash message -->
                <?php if ($this->session->flashdata('message')): ?>
                    <div class="alert <?php echo $this->session->flashdata('status'); ?> mt-3">
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                <?php endif; ?>

                <!-- Search Form -->
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

                <!-- Table Data -->
                <div class="row">
                    <div class="col-md-12">
                        <?php if (isset($students)): ?>
                            <?php
                                $table_setup = array('table_open' => '<table class="table dt-responsive nowrap table-bordered" border="1">');
                                $this->table->set_template($table_setup);

                                $print_fields = array('USN', 'Student Name', 'CGPA');
                                $this->table->set_heading($print_fields);

                                $semester_data = $this->admin_model->getStudentMarksBySemester($students->usn, 8);
                                $cgpa = !empty($semester_data) ? number_format($semester_data[0]->cgpa ?? 0, 2) : 'N/A';

                                $result_array = array(
                                    $students->usn,
                                    $students->student_name,
                                    $cgpa,
                                );
                                $this->table->add_row($result_array);

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
                    </div>
                </div>

                <!-- PDC Preview -->
                <?php if (isset($students)): ?>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                 style="background-color:#2f4050; padding: 2px 10px; font-size: 14px;">
                                <h3 class="card-title text-white mt-2">PDC Preview</h3>
                                <div class="card-tools d-flex">
                                    <a href="<?= base_url('admin/generate_pdc_pdf/' . base64_encode($students->id)) ?>" 
                                       class="btn btn-primary btn-sm btn-icon-split shadow-sm ml-2">
                                        <span class="icon"><i class="fas fa-download"></i></span> 
                                        <span class="text">Download PDC</span>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <iframe src="<?= base_url('admin/generate_pdc_pdf_prev/' . base64_encode($students->id)) ?>#toolbar=0&navpanes=0&scrollbar=0" width="100%" height="700px" style="border: none;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>


<!-- Styles -->


<!-- Scripts -->
<script>
    // Remove spaces while typing USN
    document.getElementById('usn').addEventListener('input', function() {
        this.value = this.value.replace(/\s+/g, '');
    });

    // Show loader on form submit
    document.getElementById('pdc_search').addEventListener('submit', function () {
        document.getElementById('loader-popup').style.display = 'block';
    });
</script>
