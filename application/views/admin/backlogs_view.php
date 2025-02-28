<html lang="en">

<body>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 font-size-18">Student Backlogs</h4>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <!-- Flash message (if any) -->
                    <?php if ($this->session->flashdata('message')): ?>
                        <div class="alert <?php echo $this->session->flashdata('status'); ?> mt-3">
                            <?php echo $this->session->flashdata('message'); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Filter Form (Aligning with your teammate's design) -->
                    <form class="user" id="backlog_filter_form">
                        <div class="row">
                            <!-- Admission Year Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="admission_year">Admission Year <span class="text-danger">*</span></label>
                                    <select name="admission_year" class="form-control" id="admission_year">
                                        <option value="">Select Admission Year</option>
                                        <?php foreach ($admission_years as $year) { ?>
                                            <option value="<?= $year->admission_year ?>"><?= $year->admission_year ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('admission_year'); ?></span>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-block mt-4" id="FilterBtn">Filter</button>
                            </div>
                        </div>
                    </form>

                    <!-- No Data Message Section (Initially shown) -->
                    <div id="no_data_message" class="text-center">
                        <img src="<?= base_url('assets/images/no_data.jpg') ?>" alt="No Data" class="img-fluid" />
                        <p>Please select the year</p>
                    </div>

                    <!-- Table Section (Initially hidden) -->
                    <div id="table_section" style="display: none;">
                        <div class="row mt-4">
                            <div class="col-md-12">
                            <div class="table-responsive">

                                <!-- Change the table ID to "backlog-datatable" -->
<table class="table table-bordered dt-responsive nowrap" id="backlog-datatable">
    <thead class="thead-dark">
        <tr>
            <th>USN</th>
            <th>Name</th>
            <th>Admission Year</th>
            <th>Programme</th>
            <th>Branch</th>
            <th>Subject Code</th>
            <th>Grade</th>
        </tr>
    </thead>
    <tbody>
        <!-- Data will be inserted here by AJAX -->
    </tbody>
</table>

                            </div>
                                        </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


<script>
    $(document).ready(function() {
    // Initially hide the image and show the "Please select the year" text
    $('#no_data_message img').hide();
    $('#no_data_message p').text("Please select the year").show();
    $('#table_section').hide();

    // Initialize DataTable with new ID "backlog-datatable"
    var table = $('#backlog-datatable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "pageLength": 10,
        "language": {
            "paginate": {
                "previous": "<", 
                "next": ">"
            }
        }
    });

    // Filter form submit (AJAX request)
    $('#backlog_filter_form').submit(function(e) {
        e.preventDefault();
        var admission_year = $('#admission_year').val();

        if (!admission_year) {
            $('#no_data_message img').hide();
            $('#no_data_message p').text("Please select the year").show();
            $('#table_section').hide();
            return;
        }

        // AJAX request to fetch backlog data
        $.ajax({
            url: "<?= base_url('admin/fetch_backlogs') ?>",
            type: "POST",
            data: { admission_year: admission_year },
            dataType: "json",
            success: function(data) {
                table.clear().draw(); // Clear existing data

                if (data.length > 0) {
                    $.each(data, function(index, student) {
                        table.row.add([
                            student.usn,
                            student.student_name,
                            student.admission_year,
                            student.programme,
                            student.branch,
                            student.course_code,
                            student.grade
                        ]).draw();
                    });

                    $('#no_data_message').hide();
                    $('#table_section').show();
                } else {
                    // No backlog found, show the image and message
                    $('#table_section').hide();
                    $('#no_data_message img').show();
                    $('#no_data_message p').text("No backlogs found").show();
                    $('#no_data_message').show();
                }
            }
        });
    });
});

</script>


</body>

</html>