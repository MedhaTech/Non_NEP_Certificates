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
                <!-- Flash messages -->
                <?php if($this->session->flashdata('message')): ?>
                    <div class="alert <?php echo $this->session->flashdata('status'); ?> mt-3">
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                <?php endif; ?>

                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="programme">Programme:</label>
                        <select class="form-control" id="programme" name="programme">
                            <option value="">All</option>
                            <?php foreach ($programmes as $programme): ?>
                                <option value="<?= $programme->programme ?>"><?= $programme->programme ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="branch">Branch:</label>
                        <select class="form-control" id="branch" name="branch">
                            <option value="">All</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch->branch ?>"><?= $branch->branch ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="semester">Semester:</label>
                        <select class="form-control" id="semester" name="semester">
                            <option value="">All</option>
                            <?php foreach ($semesters as $semester): ?>
                                <option value="<?= $semester->semester ?>"><?= $semester->semester ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table dt-responsive nowrap table-bordered" id="basic-datatable">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Branch</th>
                                <th>Semester</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($students as $admissions1):
                                $edit_url = base_url('admin/editcourse/' . $admissions1->id);
                                $encryptId = base64_encode($admissions1->id);
                                $delete_url = base_url('admin/deleteCourse/' . $encryptId);
                            ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= anchor('admin/viewcourseDetails/'.$encryptId, $admissions1->course_code); ?></td>
                                <td><?= $admissions1->course_name; ?></td>
                                <td><?= $admissions1->branch; ?></td>
                                <td><?= $admissions1->semester; ?></td>
                                <td>
                                    <a href="<?= $edit_url ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                    <a href="<?= $delete_url ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?')"><i class="fa fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $("#programme, #branch, #semester").change(function(){
        var programme = $("#programme").val();
        var branch = $("#branch").val();
        var semester = $("#semester").val();

        $.ajax({
            url: "<?= base_url('admin/filterCourses') ?>",
            method: "POST",
            data: {programme: programme, branch: branch, semester: semester},
            success: function(response){
                $("#basic-datatable tbody").html(response);
            }
        });
    });
});
</script>
