<!-- Content Wrapper. Contains page content -->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18"><?= $page_title; ?></h4>
                    <?php echo anchor('admin/add_newcourse', '<span class="icon"><i class="fas fa-plus"></i></span><span class="text"> Add Course</span>', 'class="btn btn-danger btn-sm"'); ?>
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

                <?php echo form_open('admin/courses', 'class="user" id="filter_courses"'); ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="programme">Programme</label>
                            <?php echo form_dropdown('programme', $programme_options, isset($selected_programme) ? $selected_programme : 'All', 'class="form-control" id="programme"'); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="branch">Branch</label>
                            <?php echo form_dropdown('branch', $branch_options, isset($selected_branch) ? $selected_branch : 'All', 'class="form-control" id="branch"'); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="semester">Semester</label>
                            <?php echo form_dropdown('semester', $semester_options, isset($selected_semester) ? $selected_semester : 'All', 'class="form-control" id="semester"'); ?>
                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </div>
                </div>
                </form>

                <div class="row">
    <div class="col-md-12">
        <?php
        if (isset($courses) && count($courses) > 0) {
            // Set up the table
            $table_setup = array('table_open' => '<table class="table dt-responsive nowrap table-bordered" border="1" id="basic-datatable">');
            $this->table->set_template($table_setup);

            // Table column headers

            $print_fields = array('S.NO', 'Course Code', 'Course Name', 'Branch', 'Semester', 'Actions');

            $this->table->set_heading($print_fields);

            // Populate the table with course data
            $i = 1;
            foreach ($courses as $course) {
                $edit_url = base_url('admin/editcourse/' . $course->id);
                $encryptId = base64_encode($course->id);
                $delete_url = base_url('admin/deleteCourse/' . $encryptId);

                $result_array = array(
                    $i++,
                    anchor('admin/viewcourseDetails/' . $encryptId, $course->course_code),
                    $course->course_name,
                    $course->branch,
                    $course->semester,
                    "<a href='{$edit_url}' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i> Edit</a> 
                     <a href='#' class='btn btn-danger btn-sm' onclick='openDeleteModal(\"$delete_url\")'><i class='fa fa-trash'></i> Delete</a>"
                );
                $this->table->add_row($result_array);
            }
            echo $this->table->generate();
        } else {
            // Check if it's the first load or after a filter
            if (isset($selected_programme) || isset($selected_branch) || isset($selected_semester)) {
                // If it's after applying a filter and no data is found, show the 'no data' image
                echo "<div class='text-center'><img src='" . base_url() . "assets/images/no_data.jpg' class='nodata'></div>";
            } else {
                // If it's the first load, do not show the image, display a custom message instead
                echo "<div class='text-center'>No courses available at the moment. Please apply a filter to view courses.</div>";
            }
        }
        ?>
    </div>
</div>

            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
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
                Are you sure you want to delete this course?
            </div>
            <div class="modal-footer">
                <a href="<?= base_url('admin/courses'); ?>" class="btn btn-secondary">Cancel</a>
                <a id="deleteConfirmButton" href="#" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function openDeleteModal(deleteUrl) {
        document.getElementById('deleteConfirmButton').setAttribute('href', deleteUrl);
        $('#deleteModal').modal('show');
    }
</script>