<div class="page-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"
                style="background-color:#2f4050;">
                <h3 class="card-title text-white"><?= $page_title; ?></h3>
                <div class="card-tools d-flex">
                    <?php echo anchor('admin/editstudent/' . $students->id, '<span class="icon"><i class="fas fa-edit"></i></span> <span class="text">Edit</span>', 'class="btn btn-danger btn-sm btn-icon-split shadow-sm"'); ?>
                    <?php echo anchor('admin/generate_transcript_pdf/' . $students->id, 
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

        <div class="section">
            <?php for ($semester = 1; $semester <= 8; $semester++): ?>
            <div class="card">
                <!-- Semester Header Section with Accordion -->
                <div class="card-header d-flex justify-content-between align-items-center"
                    style="background-color:#2f4050;">
                    <h4 class="card-title text-white">Semester <?= $semester; ?></h4>
                    <div class="card-tools d-flex">
                        <!-- Print button for this semester -->
                        <?php 

$session = date("F Y", strtotime($course->result_year));

    ;

echo anchor('admin/generate_student_pdf/' . $students->id . '/' . $semester, 
    '<span class="icon"><i class="fas fa-print"></i></span>', 
    'class="btn btn-light btn-sm me-2 print-semester" 
    data-usn="' . $students->usn . '" 
    data-semester="' . $semester . '" 
    data-session="' . $session . '"'); 
?>


                        <!-- Existing collapse button -->
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
                                    <th>CIE</th>
                                    <th>SEE</th>
                                    <th>Grade</th>
                                    <th>Grade Points</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty(${"semester_$semester"})): ?>
                                <?php $sno = 1; ?>
                                <?php foreach (${"semester_$semester"} as $course): ?>
                                <tr>
                                    <td><?= $sno++; ?></td>
                                    <td><?= $course->course_code; ?></td>
                                    <td><?= $course->course_name; ?></td>
                                    <td><?= $course->cie; ?></td>
                                    <td><?= $course->see; ?></td>
                                    <td><?= $course->grade; ?></td>
                                    <td><?= $course->grade_points; ?></td>
                                    <td><?= $course->result_year ; ?></td>
                                    <!-- <td>
                                        <a href="<?= site_url('admin/edit_marks/' . $course->usn); ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                    </td> -->
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No courses found for this semester.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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

</div>


<!-- for student grade card -->
<script>
$(document).ready(function() {
    function logCertificate(usn, details) {
        $.ajax({
            url: "<?= site_url('admin/update_certificate_log') ?>",
            type: "POST",
            data: { usn: usn, details: details },
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
                day: "2-digit", month: "2-digit", year: "numeric", 
                hour: "2-digit", minute: "2-digit"
            });
        }
    });
</script>
