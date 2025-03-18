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
                    <?php if ($this->session->flashdata('message')): ?>
                        <div class="alert <?php echo $this->session->flashdata('status'); ?> mt-3">
                            <?php echo $this->session->flashdata('message'); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Filter Form -->
                    <form class="user" id="backlog_filter_form">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="admission_year">Admission Year <span class="text-danger">*</span></label>
                                    <?php
                                    usort($admission_years, function($a, $b) {
                                        return $b->admission_year - $a->admission_year;
                                    });
                                    ?>
                                    <select name="admission_year" class="form-control" id="admission_year">
                                        <option value="">Select Admission Year</option>
                                        <?php foreach ($admission_years as $year) { ?>
                                            <option value="<?= $year->admission_year ?>"><?= $year->admission_year ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('admission_year'); ?></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-block mt-4" id="FilterBtn">Filter</button>
                            </div>
                        </div>
                    </form>

                    <!-- No Data Message -->
                    <div id="no_data_message" class="text-center">
                        <img src="<?= base_url('assets/images/no_data.jpg') ?>" alt="No Data" class="img-fluid" />
                        <p>Please select the year</p>
                    </div>

                    <!-- Table Section -->
                    <div id="table_section" style="display: none;">
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="table-responsive">
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
                                            <!-- Filled dynamically -->
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
        $(document).ready(function () {
            $('#no_data_message img').hide();
            $('#no_data_message p').text("Please select the year").show();
            $('#table_section').hide();

            let table;

            $('#backlog_filter_form').on('submit', function (e) {
                e.preventDefault();
                const admission_year = $('#admission_year').val();

                if (!admission_year) {
                    $('#no_data_message img').show();
                    $('#no_data_message p').text("Please select the year").show();
                    $('#table_section').hide();
                    return;
                }

                $('#no_data_message').hide();
                $('#table_section').show();

                if ($.fn.DataTable.isDataTable('#backlog-datatable')) {
                    table.ajax.reload();
                } else {
                    table = $('#backlog-datatable').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": "<?= base_url('admin/fetch_backlogs') ?>",
                            "type": "POST",
                            "data": function (d) {
                                d.admission_year = $('#admission_year').val();
                            },
                            "beforeSend": function () {
                                $('#loader-popup').fadeIn();
                            },
                            "complete": function () {
                                $('#loader-popup').fadeOut();
                            }
                        },
                        "columns": [
                            { "data": "usn" },
                            { "data": "student_name" },
                            { "data": "admission_year" },
                            { "data": "programme" },
                            { "data": "branch" },
                            { "data": "course_code" },
                            { "data": "grade" }
                        ],
                        "pageLength": 10,
                        "lengthChange": true,
                        "searching": true,
                        "ordering": true,
                        "responsive": true,
                        "language": {
                            "paginate": {
                                "previous": "<",
                                "next": ">"
                            },
                            "emptyTable": "No backlogs found for selected year",
                            "processing": "" // Remove default text
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
