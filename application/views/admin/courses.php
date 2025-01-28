<!-- Content Wrapper. Contains page content -->
<div class="page-content">
    <div class="container-fluid">
        <div class="card">

            <div class="card-body">


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
                                $print_fields = array('S.NO', 'Course Code', 'Course Name', 'Branch', 'Semester', 'Year', 'Crhrs', 'Course Order', 'Actions');
                                $this->table->set_heading($print_fields);

                                $i = 1;
                                foreach ($students as $course) {
                                    // Encrypting ID for URL safety
                                    // $encryptId = base64_encode($admissions1->id);

                                    // Edit and Delete URLs
                                    $edit_url = base_url('admin/editcourse/' . $course->id);  // Adjust URL if necessary
                                    // $delete_url = base_url('admin/deleteCourse/' . $encryptId);  // Delete URL with encrypted ID
                                    // Encrypting ID for URL safety
                                $encryptId = base64_encode($course->id);

                                  // Delete URL
                                $delete_url = base_url('admin/deleteCourse/' . $encryptId); // Delete URL with encrypted ID


                                    // Filling table rows dynamically with the "Edit" and "Delete" buttons
                                    $result_array = array(
                                        $i++,
                                        $course->course_code,
                                        $course->course_name,
                                        $course->branch,
                                        $course->semester,
                                        $course->year,
                                        $course->crhrs,
                                        $course->course_order,
                                        // Add Edit and Delete buttons/links
                                        "<a href='{$edit_url}' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i> Edit</a> 
                                        <a href='{$delete_url}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this course?\")'><i class='fa fa-trash'></i> Delete</a>"
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
    </div>
</div>
<!-- /.content-wrapper -->