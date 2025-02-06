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
                    <?php if($this->session->flashdata('message')): ?>
                        <div class="alert <?php echo $this->session->flashdata('status'); ?> mt-3">
                            <?php echo $this->session->flashdata('message'); ?>
                        </div>
                    <?php endif; ?>
                <div class="row">
                    <div class="col-md-12">
                        <!-- <div class="card">
                            <div class="card-body"> -->
                                <?php
                                    if (count($students)) {
                                        // Table setup
                                        $table_setup = array('table_open' => '<table class="table dt-responsive nowrap table-bordered" border="1" id="basic-datatable">');
                                        $this->table->set_template($table_setup);

                                        // Table headings
                                        $print_fields = array('S.NO', 'USN', 'Student Name', 'Actions', 'Branch');
                                        $this->table->set_heading($print_fields);

                                        $i = 1;
                                        foreach ($students as $student) {
                                            $edit_url = base_url('admin/editstudent/' . $student->id);  // Adjust URL if necessary
                                            $encryptId = base64_encode($student->id);
            
                                            $delete_url = base_url('admin/deletestudent/' . $encryptId); // Delete URL with encrypted ID

                                            // Filling table rows dynamically
                                            $result_array = array(
                                                $i++,
                                                anchor('admin/viewstudentDetails/'.$encryptId,  $student->usn),
                                                $student->student_name,
                                                "<a href='{$edit_url}' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i> Edit</a> 
                                                 <a href='{$delete_url}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this Student?\")'><i class='fa fa-trash'></i> Delete</a>",
                                                 $student->branch
                                            );
                                            $this->table->add_row($result_array);
                                        }
                                        // Generating and displaying the table
                                        echo $this->table->generate();
                                        } else {
                                            // No data available message
                                            echo "<div class='text-center'><img src='" . base_url() . "assets/img/no_data.jpg' class='nodata'></div>";
                                        }
                                    ?>
                            <!-- </div> end card body -->
                        <!-- </div> end card -->
                    </div><!-- end col-->
                </div>

            </div>
          </div>
        <!-- </div>
        </div> -->
    </div>
</div>
<!-- /.content-wrapper -->