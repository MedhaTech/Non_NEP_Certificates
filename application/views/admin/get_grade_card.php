

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
                        <div id="semester-selection" style="display: none;">
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

<script>
$(document).ready(function() {
    // Keyup event for USN input with debounce
    var timer;
    $('#usn').on('keyup', function() {
        clearTimeout(timer);
        var usn = $(this).val().trim();
        
        // Clear previous error
        $('#usn-error').html('');
        
        // Hide semester selection if USN is cleared
        if (usn === '') {
            $('#semester-selection').hide();
            return;
        }
        
        // Debounce the AJAX request to avoid too many requests
        timer = setTimeout(function() {
            // Check if USN has at least 3 characters before making request
            if (usn.length >= 3) {
                // Show loading indicator
                $('#usn-error').html('<small class="text-info"><i class="fas fa-spinner fa-spin"></i> Searching...</small>');
                
                $.ajax({
                    url: '<?= site_url('admin/fetch_semester_options'); ?>',
                    type: 'POST',
                    data: { usn: usn },
                    dataType: 'json',
                    success: function(response) {
                        // Clear loading indicator
                        $('#usn-error').html('');
                        
                        if (response.status === 'success') {
                            // Populate dropdown options
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
        }, 300); // Wait 300ms after user stops typing
    });
    
    // Function to populate semester dropdown
    function populateSemesterOptions(options) {
        var selectElement = $('#semester_option');
        selectElement.empty();
        selectElement.append('<option value="">Select a Semester/Attempt</option>');
        
        // Regular semesters first
        var regularSemesters = options.filter(function(option) {
            return option.type === 'regular';
        });
        
        // Sort regular semesters numerically
        regularSemesters.sort(function(a, b) {
            return parseInt(a.semester) - parseInt(b.semester);
        });
        
        if (regularSemesters.length > 0) {
            selectElement.append('<optgroup label="Regular Semesters">');
            
            $.each(regularSemesters, function(index, option) {
               
                var formattedLabel = 'Semester ' + option.semester + ' (' + option.year + ')';
                selectElement.append('<option value="' + option.label + '">' + formattedLabel + '</option>');
            });
            
            selectElement.append('</optgroup>');
        }
        
        // Supplementary attempts
        var supplementaryAttempts = options.filter(function(option) {
            return option.type === 'supplementary';
        });
        
        if (supplementaryAttempts.length > 0) {
            selectElement.append('<optgroup label="Supplementary Attempts">');
            
            supplementaryAttempts.forEach(function(option) {
                var semesterList = option.semesters.map(function(sem) {
                    return 'Sem ' + sem;
                }).join(', ');
                
                var formattedLabel = 'Supplementary ' + option.sequence + 
                                   ' (' + option.year + ')  ' ;
                selectElement.append('<option value="' + option.label + '">' + 
                                   formattedLabel + '</option>');
            });
            
            selectElement.append('</optgroup>');
        }
    }
    
    // Generate grade card button click
    $('#generate-btn').on('click', function() {
        var usn = $('#usn').val().trim();
        var semesterOption = $('#semester_option').val();
        
        const errorMessageDiv = document.getElementById('error-message');

if (usn === '' || semesterOption === '') {
    errorMessageDiv.textContent = 'Please select a USN and semester/attempt';
    return;
} else {
    errorMessageDiv.textContent = ''; // Clear the error message if inputs are valid
}

        
        // Show loading
        $('#grade-card-result').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Generating grade card...</p></div>');
        
        // Generate grade card
        $.ajax({
            url: '<?= site_url('admin/generate_grade_card'); ?>',
            type: 'POST',
            data: { 
                usn: usn,
                semester_option: semesterOption
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#grade-card-result').html(response.html);
                } else {
                    $('#grade-card-result').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#grade-card-result').html('<div class="alert alert-danger">An error occurred while generating the grade card.</div>');
            }
        });
    });
});
</script> 