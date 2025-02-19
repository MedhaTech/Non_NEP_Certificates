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
                                <table class="table table-bordered dt-responsive nowrap" id="backlogs_table">
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

    <script>
        $(document).ready(function() {
            // Initial state: show the image and message, hide the table
            $('#no_data_message').show();
            $('#table_section').hide();

            // Filter form submit (AJAX request)
            $('#backlog_filter_form').submit(function(e) {
                e.preventDefault(); // Prevent page reload on form submit
                var admission_year = $('#admission_year').val();

                // If no admission year is selected, show the message and hide the table
                if (!admission_year) {
                    $('#no_data_message').show();
                    $('#table_section').hide(); // Hide table if no year selected
                    return; // Stop further processing
                }

                // Otherwise, make the AJAX call
                $.ajax({
                    url: "<?= base_url('admin/fetch_backlogs') ?>",
                    type: "POST",
                    data: {
                        admission_year: admission_year
                    },
                    dataType: "json",
                    success: function(data) {
                        var tableBody = "";
                        if (data.length > 0) {
                            $.each(data, function(index, student) {
                                tableBody += "<tr>" +
                                    "<td>" + student.usn + "</td>" +
                                    "<td>" + student.student_name + "</td>" +
                                    "<td>" + student.admission_year + "</td>" +
                                    "<td>" + student.programme + "</td>" +
                                    "<td>" + student.branch + "</td>" +
                                    "<td>" + student.subcode + "</td>" +
                                    "<td>" + student.grade + "</td>" +
                                    "</tr>";
                            });
                            $('#no_data_message').hide(); // Hide the message when data is found
                            $('#table_section').show(); // Show the table
                        } else {
                            tableBody = "<tr><td colspan='7' class='text-center'>No backlogs found.</td></tr>";
                            $('#no_data_message').hide(); // Hide the message when data is found
                            $('#table_section').show(); // Show the table even if no backlogs found
                        }
                        $('#backlogs_table tbody').html(tableBody);
                    }
                });
            });
        });
    </script>
</body>

</html>