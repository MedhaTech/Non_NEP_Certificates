<div class="page-content">
    <div class="container-fluid">
        <div class="card p-4 shadow-sm">
            <div class="card-header text-white text-center">
                <h5 class="card-title mb-0">Download Transcript</h5>
            </div>
            <div class="card-body">
                <?php echo form_open_multipart('admin/transcript', 'class="user"'); ?>

                <div class="d-flex align-items-center gap-3">
                    <input type="text" class="form-control bg-white border shadow-sm p-2" placeholder="Enter USN"
                        id="usn" name="usn" style="width: 200px;"
                        value="<?php echo (set_value('usn')) ? set_value('usn') : $usn; ?>">
                    <div style="padding: 0px 25px 0px 25px;"></div>
                    
                    <div style="padding: 0px 25px 0px 25px;"></div>

                    <button class="btn btn-primary px-4" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>

        <?php if (empty($students)): ?>
            <div class="alert alert-warning text-center mt-3">No data found for the entered USN.</div>
        <?php endif; ?>

        <div class="card-body" id="student-info" style="display: none;">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center"
                    style="background-color:#2f4050;">
                    <h3 class="card-title text-white"><?= $page_title; ?></h3>
                    <div class="card-tools d-flex">
                        <?php echo anchor('admin/editstudent/' . $students->id, '<span class="icon"><i class="fas fa-edit"></i></span> <span class="text">Edit</span>', 'class="btn btn-danger btn-sm btn-icon-split shadow-sm"'); ?>
                        <?php echo anchor(
                            'admin/generate_transcript_pdf/' . $students->id,
                            '<span class="icon"><i class="fas fa-file-pdf"></i></span> <span class="text">Generate Transcript</span>',
                            'class="btn btn-primary btn-sm btn-icon-split shadow-sm ml-2 generate-transcript" data-usn="' . $students->usn . '"'
                        );
                        ?>
                        <?php echo anchor('admin/students', '<span class="icon"><i class="fas fa-arrow-left"></i></span> <span class="text">Close</span>', 'class="btn btn-secondary btn-sm btn-icon-split shadow-sm ml-2"'); ?>
                    </div>
                </div>
                <div class="card-body" id="student-details">
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

            <table class="table table-bordered mt-3" id="logs-table-container" style="display: none;">
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





<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

<!-- Edit Marks Modal -->
<div class="modal fade" id="editMarksModal" tabindex="-1" role="dialog" aria-labelledby="editMarksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content tx-14">
            <div class="modal-header">
                <h5 class="modal-title" id="editMarksModalLabel">Edit Course Marks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="messageContainer"></div>
                <!-- Form to edit course marks -->
                <form id="editMarksForm" class="form-horizontal">
                    <div class="row">
                        <div class="form-group">
                            <!-- <input type="hidden" class="form-control" id="stu_usn" name="stu_usn"> -->
                            <input type="hidden" class="form-control" name="usn" id="usn"
                                value="<?php echo set_value('usn', $studentmarks->usn); ?>">
                            <span class="text-danger"><?php echo form_error('usn'); ?></span>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="course_code">Course Code</label>
                                <input type="text" class="form-control" name="course_code" id="course_code"
                                    value="<?php echo set_value('course_code', $studentmarks->course_code); ?>">
                                <span class="text-danger"><?php echo form_error('course_code'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="course_name">Course Name</label>
                                <input type="text" class="form-control" name="course_name" id="course_name"
                                    value="<?php echo set_value('course_name', $studentmarks->course_name); ?>">
                                <span class="text-danger"><?php echo form_error('course_name'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cie">CIE</label>
                                <!-- <input type="number" class="form-control" id="cie" name="cie"> -->
                                <input type="text" class="form-control" name="cie" id="cie"
                                    value="<?php echo set_value('cie', $studentmarks->cie); ?>">
                                <span class="text-danger"><?php echo form_error('cie'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="see">SEE</label>
                                <input type="text" class="form-control" name="see" id="see"
                                    value="<?php echo set_value('see', $studentmarks->see); ?>">
                                <span class="text-danger"><?php echo form_error('see'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cie_see">CIE_SEE</label>
                                <input type="text" class="form-control" name="cie_see" id="cie_see"
                                    value="<?php echo set_value('cie_see', $studentmarks->cie_see); ?>">
                                <span class="text-danger"><?php echo form_error('cie_see'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="grade">Grade</label>
                                <input type="text" class="form-control" name="grade" id="grade"
                                    value="<?php echo set_value('grade', $studentmarks->grade); ?>">
                                <span class="text-danger"><?php echo form_error('grade'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sgpa">Sgpa</label>
                                <input type="text" class="form-control" name="sgpa" id="sgpa"
                                    value="<?php echo set_value('sgpa', $studentmarks->sgpa); ?>">
                                <span class="text-danger"><?php echo form_error('sgpa'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cgpa">cgpa</label>
                                <input type="text" class="form-control" name="cgpa" id="cgpa"
                                    value="<?php echo set_value('cgpa', $studentmarks->cgpa); ?>">
                                <span class="text-danger"><?php echo form_error('cgpa'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="semester">Semester</label>
                                <input type="text" class="form-control" name="semester" id="semester"
                                    value="<?php echo set_value('sgpa', $studentmarks->semester); ?>">
                                <span class="text-danger"><?php echo form_error('semester'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="grade_points">Grade Points</label>
                                <input type="text" class="form-control" name="grade_points" id="grade_points"
                                    value="<?php echo set_value('grade_points', $studentmarks->grade_points); ?>">
                                <span class="text-danger"><?php echo form_error('grade_points'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="credits_earned">Credits Earned</label>
                                <input type="text" class="form-control" name="credits_earned" id="credits_earned"
                                    value="<?php echo set_value('credits_earned', $studentmarks->credits_earned); ?>">
                                <span class="text-danger"><?php echo form_error('credits_earned'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="credits_actual">Credits Actual</label>
                                <input type="text" class="form-control" name="credits_actual" id="credits_actual"
                                    value="<?php echo set_value('credits_actual', $studentmarks->credits_actual); ?>">
                                <span class="text-danger"><?php echo form_error('credits_actual'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ci">CI</label>
                                <input type="text" class="form-control" name="ci" id="ci"
                                    value="<?php echo set_value('ci', $studentmarks->ci); ?>">
                                <span class="text-danger"><?php echo form_error('ci'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="suborder">Sub Order</label>
                                <input type="text" class="form-control" name="suborder" id="suborder"
                                    value="<?php echo set_value('suborder', $studentmarks->suborder); ?>">
                                <span class="text-danger"><?php echo form_error('suborder'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="reexamyear">Re Exam Year</label>
                                <input type="date" class="form-control" name="reexamyear" id="reexamyear"
                                    value="<?php echo set_value('reexamyear', $studentmarks->reexamyear); ?>">
                                <span class="text-danger"><?php echo form_error('reexamyear'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="result_year">Result Year</label>
                                <input type="date" class="form-control" name="result_year" id="result_year"
                                    value="<?php echo set_value('result_year', $studentmarks->result_year); ?>">
                                <span class="text-danger"><?php echo form_error('result_year'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="exam_period">Exam Period</label>
                                <input type="text" class="form-control" name="exam_period" id="exam_period"
                                    value="<?php echo set_value('exam_period', $studentmarks->exam_period); ?>">
                                <span class="text-danger"><?php echo form_error('exam_period'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="gcno">Gc no</label>
                                <input type="text" class="form-control" name="gcno" id="gcno"
                                    value="<?php echo set_value('gcno', $studentmarks->gcno); ?>">
                                <span class="text-danger"><?php echo form_error('gcno'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="barcode">Barcode</label>
                                <input type="text" class="form-control" name="barcode" id="barcode"
                                    value="<?php echo set_value('barcode', $studentmarks->barcode); ?>">
                                <span class="text-danger"><?php echo form_error('barcode'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="torder">Torder</label>
                                <input type="text" class="form-control" name="torder" id="torder"
                                    value="<?php echo set_value('torder', $studentmarks->torder); ?>">
                                <span class="text-danger"><?php echo form_error('torder'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="texam_period">TExam Period</label>
                                <input type="text" class="form-control" name="texam_period" id="texam_period"
                                    value="<?php echo set_value('texam_period', $studentmarks->texam_period); ?>">
                                <span class="text-danger"><?php echo form_error('texam_period'); ?></span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" name="Update" id="Update">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

</div>
</div>

<script>
    function loadCourseData(course_code, course_name, credits_earned, cie, see, grade, gradePoints, usn) {
        document.getElementById('course_code').value = course_code;
        document.getElementById('course_name').value = course_name;
        // document.getElementById('credits_earned').value = credits_earned;
        // document.getElementById('cie').value = cie;
        // document.getElementById('see').value = see;
        // document.getElementById('grade').value = grade;
        // document.getElementById('gradePoints').value = gradePoints;
        // document.getElementById('stu_usn').value = usn; // Make sure 'usn' is passed here
    }
</script>

<script>
    $('#editMarksForm').submit(function(event) {
        event.preventDefault();

        var formData = $(this).serialize(); // Serialize the form data

        console.log('Form Data:', formData); // Debugging: Log the form data to check usn and other fields

        $.ajax({
            url: '<?= site_url('admin/save_marks'); ?>', // Change this to your save URL
            type: 'POST',
            data: formData,
            success: function(response) {
                // Show success message on the page
                $('#messageContainer').html('<div class="alert alert-success">Marks updated successfully!</div>');
                $('#editMarksModal').modal('hide'); // Hide the modal
                location.reload(); // Reload the page or update the table dynamically
            },
            error: function() {
                // Show error message on the page
                $('#messageContainer').html('<div class="alert alert-danger">Error updating marks.</div>');
            }
        });
    });

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
                        $('#deleteContainer').html('<div class="alert alert-success">Marks updated successfully!</div>');
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

<script>
    $(document).ready(function() {
        // Show student details and logs table when a USN is searched
        $('form.user').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            let usn = $('#usn').val(); // Get the USN value

            if (usn) {
                $('#student-info').show(); // Show student details
                $('#logs-table-container').show(); // Show logs table
                // ... existing AJAX call to fetch student data ...
            }
        });
    });
</script>