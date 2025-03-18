<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18"><?= $page_title; ?></h4>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="usn">Enter Student USN</label>
                            <input type="text" class="form-control" id="usn" name="usn" placeholder="Enter USN" autocomplete="off">
                            <div id="usn-error" class="text-danger mt-2"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div id="semester-selection">
                            <div class="form-group">
                                <label for="semester_option">Select Semester/Attempt</label>
                                <select class="form-control" id="semester_option" name="semester_option">
                                    <option value="">Select a Semester/Attempt</option>
                                    <!-- Options will be populated dynamically -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mt-4">
                            <button id="generate-btn" class="btn btn-primary">Generate Grade Card</button>
                        </div>
                    </div>
                </div>

                <div id="error-message" style="color: red; margin-top: 10px; text-align:center;"></div>
                <div id="grade-card-result" class="mt-4"></div>
            </div>
        </div>
    </div>
</div>

<!-- Loader -->
<div id="loader-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
    background-color: rgba(255, 255, 255, 0.8); z-index: 9999; text-align: center;">
    <div style="position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="sr-only">Loading...</span>
        </div>
        <div class="mt-2">Please wait...</div>
    </div>
</div>

<script>
$(document).ready(function() {
    var timer;

    $('#usn').on('keyup', function() {
        clearTimeout(timer);
        var usn = $(this).val().trim();
        $('#usn-error').html('');
        if (usn === '') {
            $('#semester-selection').hide();
            return;
        }

        timer = setTimeout(function() {
            if (usn.length >= 3) {
                $('#usn-error').html('<small class="text-info"><i class="fas fa-spinner fa-spin"></i> Searching...</small>');

                $.ajax({
                    url: '<?= site_url('admin/fetch_semester_options'); ?>',
                    type: 'POST',
                    data: { usn: usn },
                    dataType: 'json',
                    success: function(response) {
                        $('#usn-error').html('');
                        if (response.status === 'success') {
                            populateSemesterOptions(response.options);
                            $('#semester-selection').show();
                        } else {
                            $('#usn-error').html('<span class="text-danger">' + response.message + '</span>');
                            $('#semester-selection').hide();
                        }
                    },
                    error: function() {
                        $('#usn-error').html('<span class="text-danger">An error occurred. Please try again.</span>');
                        $('#semester-selection').hide();
                    }
                });
            }
        }, 300);
    });

    function populateSemesterOptions(options) {
        var selectElement = $('#semester_option');
        selectElement.empty();
        selectElement.append('<option value="">Select a Semester/Attempt</option>');

        var regularSemesters = options.filter(opt => opt.type === 'regular');
        regularSemesters.sort((a, b) => parseInt(a.semester) - parseInt(b.semester));

        const addedSemesters = new Set();

        if (regularSemesters.length > 0) {
            selectElement.append('<optgroup label="Regular Semesters">');
            $.each(regularSemesters, function(index, option) {
                const key = option.semester;
                if (!addedSemesters.has(key)) {
                    addedSemesters.add(key);
                    var formattedLabel = 'Semester ' + option.semester + ' (' + option.year + ')';
                    selectElement.append('<option value="' + option.label + '">' + formattedLabel + '</option>');
                }
            });
            selectElement.append('</optgroup>');
        }

        var supplementaryAttempts = options.filter(opt => opt.type === 'supplementary');

        if (supplementaryAttempts.length > 0) {
            selectElement.append('<optgroup label="Supplementary Attempts">');
            supplementaryAttempts.forEach(function(option) {
                var formattedLabel = 'Supplementary ' + option.sequence + ' (' + option.year + ')';
                selectElement.append('<option value="' + option.label + '">' + formattedLabel + '</option>');
            });
            selectElement.append('</optgroup>');
        }
    }

    $('#generate-btn').on('click', function() {
        var usn = $('#usn').val().trim();
        var semesterOption = $('#semester_option').val();
        const errorMessageDiv = document.getElementById('error-message');

        if (usn === '' || semesterOption === '') {
            errorMessageDiv.textContent = 'Please select a USN and semester/attempt';
            return;
        } else {
            errorMessageDiv.textContent = '';
        }

        // Show loader
        $('#loader-overlay').show();

        $.ajax({
            url: '<?= site_url('admin/generate_grade_card'); ?>',
            type: 'POST',
            data: { 
                usn: usn,
                semester_option: semesterOption
            },
            dataType: 'json',
            success: function(response) {
                $('#loader-overlay').hide(); // Hide loader
                if (response.status === 'success') {
                    $('#grade-card-result').html(response.html);
                } else {
                    $('#grade-card-result').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#loader-overlay').hide(); // Hide loader
                $('#grade-card-result').html('<div class="alert alert-danger">An error occurred while generating the grade card.</div>');
            }
        });
    });
});
</script>
