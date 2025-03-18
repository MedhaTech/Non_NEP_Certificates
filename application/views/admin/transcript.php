<body>
<div class="page-content">
    <div class="container-fluid">
        <!-- Header section with title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Student Transcript</h4>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <div class="row">
                    <!-- USN Search Form -->
                    <div class="col-md-6">
                        <?php echo form_open('admin/transcript', ['class' => 'user', 'id' => 'usnForm']); ?>
                        <div class="form-group">
                            <label for="usn">Enter Student USN <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Enter USN" name="usn" id="usn" required value="<?= isset($usn) ? $usn : '' ?>">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>

                    <!-- Completion Year Update Form -->
                    <?php if (isset($students)): ?>
                    <div class="col-md-6">
                        <?php echo form_open('admin/update_completion_year', ['class' => 'user', 'id' => 'completionForm']); ?>
                        <div class="form-group">
                            <label for="completion_year">Year of Completion <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="completion_year" 
                                    id="completion_year"
                                    value="<?= isset($students->completion_year) ? $students->completion_year : '' ?>" 
                                    placeholder="e.g., 2024" 
                                    required
                                >
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <?= isset($students->completion_year) ? 'Update' : 'Add' ?>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="student_id" value="<?= $students->id ?>">
                            <input type="hidden" name="usn" value="<?= $students->usn ?>">
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Transcript Preview Section -->
                <?php if (isset($students)): ?>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                 style="background-color:#2f4050; padding: 2px 10px; font-size: 14px;">
                                <h3 class="card-title text-white mt-2">Transcript Preview</h3>
                                <div class="card-tools d-flex">
                                    <?php echo anchor('admin/generate_transcript_pdf/' . base64_encode($students->id), 
                                        '<span class="icon"><i class="fas fa-download"></i></span> <span class="text">Download PDF</span>', 
                                        'class="btn btn-primary btn-sm btn-icon-split shadow-sm ml-2"'); 
                                    ?>
                                </div>
                            </div>
                            <div class="card-body p-0">
                            <iframe src="<?= base_url('admin/generate_transcript_pdf_preview/' . $students->id) ?>#toolbar=0&navpanes=0&scrollbar=0" width="100%" height="700px" style="border: none;"></iframe>

                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Flash message -->
                <?php if ($this->session->flashdata('message')): ?>
                    <div class="alert <?php echo $this->session->flashdata('status'); ?> mt-3">
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>




<script>
    // Remove spaces from USN
    document.getElementById('usn').addEventListener('input', function () {
        this.value = this.value.replace(/\s+/g, '');
    });

    // Show loader on USN form submit
    document.getElementById('usnForm').addEventListener('submit', function () {
        document.getElementById('loader-popup').style.display = 'block';
    });

    // Completion Year Update with loader
    document.getElementById('completionForm')?.addEventListener('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);

        // Show loader
        document.getElementById('loader-popup').style.display = 'block';

        fetch(form.action, {
            method: 'POST',
            body: formData,
        })
        .then(response => response.text())
        .then(data => {
            console.log('Success:', data);
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong while updating. Please try again.');
            document.getElementById('loader-popup').style.display = 'none';
        });
    });
</script>
</body>
