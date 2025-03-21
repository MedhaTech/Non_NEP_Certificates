<div class="page-content">
    <div class="container-fluid">
   
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"
                style="background-color:#2f4050; padding: 2px 10px; font-size: 14px;">
                <h3 class="card-title text-white mt-2"><?= $page_title; ?></h3>
                <div class="card-tools d-flex">
                    <?php echo anchor('admin/editstudent/' . $students->id, '<span class="icon"><i class="fas fa-edit"></i></span> <span class="text">Edit</span>', 'class="btn btn-danger btn-sm btn-icon-split shadow-sm"'); ?>
                    <?php echo anchor('admin/generate_transcript_pdf/' . base64_encode($students->id), 
    '<span class="icon"><i class="fas fa-file-pdf"></i></span> <span class="text">Generate Transcript</span>', 
    'class="btn btn-primary btn-sm btn-icon-split shadow-sm ml-2 generate-transcript" data-usn="' . $students->usn . '"'); 
?>
                    <?php echo anchor('admin/students', '<span class="icon"><i class="fas fa-arrow-left"></i></span> <span class="text">Close</span>', 'class="btn btn-secondary btn-sm btn-icon-split shadow-sm ml-2"'); ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">USN</label>
                            <p><?= $students->usn; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Student Name</label>
                            <p><?= $students->student_name; ?></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Admission Year</label><br>
                            <p><?= $students->admission_year; ?></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Programme</label>
                            <p><?= $students->programme; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Branch</label>
                            <p><?= $students->branch; ?></p>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Date of Birth</label>
                            <p><?= $students->date_of_birth; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Gender</label><br>
                            <?= $students->gender; ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Category</label><br>
                            <?= $students->category; ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Mobile</label><br>
                            <?= $students->mobile; ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Parent Mobile</label>
                            <p><?= $students->parent_mobile; ?></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Father Name</label>
                            <p><?= $students->father_name; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Mother Name</label>
                            <p><?= $students->mother_name; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($this->session->flashdata('message')) : ?>
    <div class="alert alert-info"><?php echo $this->session->flashdata('message'); ?></div>
<?php endif; ?>
        <div id="deleteContainer"></div>
        <div class="section">
           <!-- Legend Bar -->
           <div class="alert alert-light border" style="font-size: 14px;">
   
    <span class="fw-bold">CR</span> - Credits Registered, 
    <span class="fw-bold">CE</span> - Credits Earned, 
    <span class="fw-bold">CCE</span> - Cumulative Credits Earned, 
    <span class="fw-bold">SGPA</span> - Semester GPA, 
    <span class="fw-bold">CGPA</span> - Cumulative GPA
</div>

<?php 
$cumulative_credits_earned = 0;
for ($semester = 1; $semester <= 8; $semester++): 
?>
    <?php 
        $total_credits_actual = 0;
        $total_credits_earned = 0;
        $sgpa = '';
        $cgpa = '';

        if (!empty($studentmarks[$semester])) {
            foreach ($studentmarks[$semester] as $course_summary) {
                $total_credits_actual += $course_summary->credits_actual ?? 0;
                $total_credits_earned += $course_summary->credits_earned ?? 0;
                $sgpa = $course_summary->sgpa ?? '';
                $cgpa = $course_summary->cgpa ?? '';
            }
        }

        $cumulative_credits_earned = $total_credits_earned;
        $session = date("F Y", strtotime($course->result_year));
    ?>

    <div class="card mb-3">
        <!-- Semester Header -->
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap"
            style="background-color:#2f4050; padding: 5px 15px;">
            
            <!-- Left: Semester Title -->
            <div class="text-white fw-bold" style="font-size: 16px;">
                Semester <?= $semester; ?>
            </div>

            <!-- Center: Summary Info -->
            <div class="text-center text-white" style="flex: 1; font-size: 14px;">
                CR: <?= $total_credits_actual; ?> &nbsp;
                CE: <?= $total_credits_earned; ?> &nbsp;
                CCE: <?= $cumulative_credits_earned; ?> &nbsp;
                SGPA: <?= number_format((float)$sgpa, 2); ?> &nbsp;
                CGPA: <?= number_format((float)$cgpa, 2); ?>
            </div>

            <!-- Right: Tools -->
            <div class="card-tools d-flex">
                <!-- Print button -->
                <?= anchor(
                    'admin/generate_student_pdf/' . base64_encode($students->id) . '/' . base64_encode($semester), 
                    '<i class="fas fa-print"></i>', 
                    'class="btn btn-light btn-sm me-2 print-semester" 
                    data-usn="' . $students->usn . '" 
                    data-semester="' . $semester . '" 
                    data-session="' . $session . '"'
                ); ?>

                <!-- Collapse button -->
                <button class="btn btn-link text-white" type="button" data-bs-toggle="collapse"
                    data-bs-target="#semester-<?= $semester; ?>" aria-expanded="false"
                    aria-controls="semester-<?= $semester; ?>">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
                <!-- Semester Table Section wrapped in a collapse div -->
                <div id="semester-<?= $semester; ?>" class="collapse">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SNO</th>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Credits</th>
                                    <th>CIE</th>
                                    <th>SEE</th>
                                    <th>Grade</th>
                                    <th>Grade Points</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if (!empty($studentmarks[$semester])): ?>
                                <?php $sno = 1; ?>
                                <?php foreach ($studentmarks[$semester] as $course): ?>
                                <tr>
                                    <td><?= $sno++ ?></td>
                                    <td><?= $course->course_code; ?></td>
                                    <td><?= $course->course_name; ?></td>
                                    <td><?= $course->credits_earned; ?></td>
                                    <td><?= $course->cie; ?></td>
                                    <td><?= $course->see; ?></td>
                                    <td><?= $course->grade; ?></td>
                                    <td><?= $course->grade_points; ?></td>
                                    <!-- <td><?= $course->credits_earned	; ?></td>
                                    <td><?= $course->credits_actual; ?></td>
                                    <td><?= $course->cgpa; ?></td>
                                    <td><?= $course->sgpa; ?></td> -->
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#editMarksModal<?= $course->course_code; ?>">
                                            Edit
                                        </a>
                                        <!-- Delete Button (Triggers Modal) -->
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" 
    data-action="<?php echo site_url('admin/deletemarks/' . $course->id . '/' .  $students->id); ?>">
    Delete
</button>
                                        

</form>
                                    </td>
                                </tr>
                                        <div class="modal fade" id="editMarksModal<?= $course->course_code; ?>" tabindex="-1"
                                            role="dialog" aria-labelledby="editMarksModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content tx-14">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editMarksModalLabel">Edit Course Marks</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="messageContainer"></div>
                                                        <!-- Form to edit course marks -->
                                                    
                                                    <form  id="update-form" method="post" action="<?php echo site_url('admin/edit_marks/' . $course->id . '/' .  $students->id); ?>" class="form-horizontal">
                                                            <div class="row">
                                                                <div class="form-group">
                                                                    <input type="hidden" class="form-control" name="usn"
                                                                        id="usn"
                                                                        value="<?php echo set_value('usn', $students->usn); ?>">
                                                                    <span
                                                                        class="text-danger"><?php echo form_error('usn'); ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="course_code">Course Code</label>
                                                                        <input type="text" class="form-control" name="course_code" id="course_code"
                                                                            value="<?php echo set_value('course_code', $course->course_code); ?>" readonly>
                                                                        <span class="text-danger"><?php echo form_error('course_code'); ?></span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="course_name">Course Name</label>
                                                                        <input type="text" class="form-control" name="course_name" id="course_name"
                                                                            value="<?php echo set_value('course_name', $course->course_name); ?>" readonly>
                                                                        <span class="text-danger"><?php echo form_error('course_name'); ?></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
            <div class="form-group">
                <label for="cie">CIE</label>
                <input type="number" class="form-control" name="cie" id="cie" min="0" max="100" required value="<?php echo set_value('cie', $course->cie); ?>">
            </div>
        </div>

        <!-- SEE -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="see">SEE</label>
                <input type="number" class="form-control" name="see" id="see" min="0" max="100" required value="<?php echo set_value('see', $course->see); ?>">
            </div>
        </div>

        <!-- CIE_SEE -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="cie_see">CIE_SEE</label>
                <input type="number" class="form-control" name="cie_see" id="cie_see" min="0" max="100" required value="<?php echo set_value('cie_see', $course->cie_see); ?>">
            </div>
        </div>

        <!-- Grade -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="grade">Grade</label>
                <input type="text" class="form-control" name="grade" id="grade" maxlength="2" required value="<?php echo set_value('grade', $course->grade); ?>">
            </div>
        </div>

        <!-- SGPA -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="sgpa">SGPA</label>
                <input type="number" class="form-control" name="sgpa" id="sgpa" min="0" max="10" step="0.01" required value="<?php echo set_value('sgpa', $course->sgpa); ?>">
            </div>
        </div>

        <!-- CGPA -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="cgpa">CGPA</label>
                <input type="number" class="form-control" name="cgpa" id="cgpa" min="0" max="10" step="0.01" required value="<?php echo set_value('cgpa', $course->cgpa); ?>">
            </div>
        </div>

        <!-- Semester -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="semester">Semester</label>
                <input type="number" class="form-control" name="semester" id="semester" min="1" max="10" required value="<?php echo set_value('semester', $course->semester); ?>">
            </div>
        </div>

        <!-- Grade Points -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="grade_points">Grade Points</label>
                <input type="number" class="form-control" name="grade_points" id="grade_points" min="0" required value="<?php echo set_value('grade_points', $course->grade_points); ?>">
            </div>
        </div>

        <!-- Credits Earned -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="credits_earned">Credits Earned</label>
                <input type="number" class="form-control" name="credits_earned" id="credits_earned" min="0" required value="<?php echo set_value('credits_earned', $course->credits_earned); ?>">
            </div>
        </div>

        <!-- Credits Actual -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="credits_actual">Credits Actual</label>
                <input type="number" class="form-control" name="credits_actual" id="credits_actual" min="0" required value="<?php echo set_value('credits_actual', $course->credits_actual); ?>">
            </div>
        </div>

        <!-- CI -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="ci">CI</label>
                <input type="text" class="form-control" name="ci" id="ci" maxlength="10" required value="<?php echo set_value('ci', $course->ci); ?>">
            </div>
        </div>

        <!-- Sub Order -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="suborder">Sub Order</label>
                <input type="text" class="form-control" name="suborder" id="suborder"  value="<?php echo set_value('suborder', $course->suborder); ?>">
            </div>
        </div>

        <!-- Re Exam Year -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="reexamyear">Re Exam Year</label>
                <input type="date" class="form-control" name="reexamyear" id="reexamyear"  value="<?php echo set_value('reexamyear', $course->reexamyear); ?>">
            </div>
        </div>

        <!-- Result Year -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="result_year">Result Year</label>
                <input type="date" class="form-control" name="result_year" id="result_year" required value="<?php echo set_value('result_year', $course->result_year); ?>">
            </div>
        </div>

        <!-- Exam Period -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="exam_period">Exam Period</label>
                <input type="text" class="form-control" name="exam_period" id="exam_period" required value="<?php echo set_value('exam_period', $course->exam_period); ?>">
            </div>
        </div>

        <!-- GC No -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="gcno">GC No</label>
                <input type="text" class="form-control" name="gcno" id="gcno"  value="<?php echo set_value('gcno', $course->gcno); ?>">
            </div>
        </div>

        <!-- Barcode -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="barcode">Barcode</label>
                <input type="text" class="form-control" name="barcode" id="barcode" required value="<?php echo set_value('barcode', $course->barcode); ?>">
            </div>
        </div>

        <!-- Torder -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="torder">Torder</label>
                <input type="text" class="form-control" name="torder" id="torder"  value="<?php echo set_value('torder', $course->torder); ?>">
            </div>
        </div>

        <!-- TExam Period -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="texam_period">TExam Period</label>
                <input type="text" class="form-control" name="texam_period" id="texam_period"  value="<?php echo set_value('texam_period', $course->texam_period); ?>">
            </div>
        </div>

    </div>
                                                            <button type="submit" class="btn btn-primary" name="Update"
                                                                id="Update">Update</button>
                                                        
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No courses found for this semester.</td>
                    </tr>
                    <?php endif; ?>
                    </tbody>
                    </table>
                                    </td>
                                </tr>
                                
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>


        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Details</th>
                    <th>Downloaded At</th>
                </tr>
            </thead>
            <tbody id="logs-table">
                <tr>
                    <td colspan="3" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>


   <!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered animate__animated animate__zoomIn" role="document">
        <div class="modal-content">
            <div class="modal-header  text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this course?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="post">
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

</div>


<!-- for student grade card -->
<script>
$(document).ready(function() {
    function logCertificate(usn, details) {
        $.ajax({
            url: "<?= site_url('admin/update_certificate_log') ?>",
            type: "POST",
            data: {
                usn: usn,
                details: details
            },
            success: function(response) {
                console.log("Certificate log updated:", response);
            },
            error: function(xhr, status, error) {
                console.error("Error updating log:", xhr.responseText);
            }
        });
    }

    $('.generate-transcript').on('click', function(e) {
        e.preventDefault();
        let usn = $(this).data('usn');

        logCertificate(usn, "Consolidated Grade Card");
        window.location.href = $(this).attr('href');
    });

    $('.print-semester').on('click', function(e) {
        e.preventDefault();
        let usn = $(this).data('usn');
        let semester = $(this).data('semester');
        let session = $(this).data('session');

        logCertificate(usn, `Grade Card (Sem: ${semester}, Session: ${session})`);
        window.location.href = $(this).attr('href');
    });
});
</script>


<script>
$(document).ready(function() {
    let usn = "<?= $students->usn; ?>"; // Get USN from PHP variable

    $.ajax({
        url: "<?= site_url('admin/fetch_certificate_logs'); ?>/" + usn,
        type: "GET",
        dataType: "json",
        success: function(response) {
            let tableContent = "";
            if (response.length > 0) {
                $.each(response, function(index, log) {
                    tableContent += `<tr>
                            <td>${index + 1}</td>
                            <td>${log.details}</td>
                            <td>${formatDateTime(log.download_at)}</td>
                        </tr>`;
                });
            } else {
                tableContent = `<tr><td colspan="3" class="text-center">No records found</td></tr>`;
            }
            $("#logs-table").html(tableContent);
        }
    });

    function formatDateTime(datetime) {
        let date = new Date(datetime);
        return date.toLocaleString("en-GB", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit"
        });
    }
});
</script>


<script>
// function loadCourseData(course_code, course_name, credits_earned, cie, see, grade, gradePoints, usn) {
//     document.getElementById('course_code').value = course_code;
//     document.getElementById('course_name').value = course_name;
//     // document.getElementById('credits_earned').value = credits_earned;
//     // document.getElementById('cie').value = cie;
//     // document.getElementById('see').value = see;
//     // document.getElementById('grade').value = grade;
//     // document.getElementById('gradePoints').value = gradePoints;
//     // document.getElementById('stu_usn').value = usn; // Make sure 'usn' is passed here
// }
</script>

<script>
// $('#editMarksForm').submit(function(event) {
//     event.preventDefault(); 

//     var formData = $(this).serialize(); // Serialize the form data

//     console.log('Form Data:', formData); // Debugging: Log the form data to check usn and other fields

//     $.ajax({
//         url: '<?= site_url('admin/save_marks'); ?>', // Change this to your save URL
//         type: 'POST',
//         data: formData,
//         success: function(response) {
//             // Show success message on the page
//             $('#messageContainer').html('<div class="alert alert-success">Marks updated successfully!</div>');
//             $('#editMarksModal').modal('hide'); // Hide the modal
//             location.reload(); // Reload the page or update the table dynamically
//         },
//         error: function() {
//             // Show error message on the page
//             $('#messageContainer').html('<div class="alert alert-danger">Error updating marks.</div>');
//         }
//     });
// });

function deleteCourse(usn, course_code) {
    if (confirm("Are you sure you want to delete this course?")) {
        $.ajax({
            url: '<?= site_url('admin/delete_marks'); ?>',
            type: 'POST',
            data: {
                usn: usn,
                course_code: course_code
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#deleteContainer').html(
                        '<div class="alert alert-success">Marks updated successfully!</div>');
                    location.reload();
                } else {
                    alert("Error deleting course. Please try again.");
                }
            },
            error: function() {
                $('#deleteContainer').html('<div class="alert alert-danger">Error updating marks.</div>');
            }
        });
    }
}
</script>


<!-- jQuery Script to Set Form Action Dynamically -->
<script>
$(document).ready(function() {
    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var action = button.data('action');
        $('#deleteForm').attr('action', action);
    });
});
</script>


