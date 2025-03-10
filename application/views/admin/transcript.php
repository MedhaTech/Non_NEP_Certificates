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
                <?php if ($this->session->flashdata('message')): ?>
                <div class="alert <?php echo $this->session->flashdata('status'); ?> mt-3">
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
                <?php endif; ?>

                <?php echo form_open_multipart('admin/transcript', 'class="user"'); ?>
                <div class="row ">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="usn">Enter Student USN <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Enter USN" name="usn" id="usn" required>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>

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
                                        'class="btn btn-danger btn-sm btn-icon-split shadow-sm ml-2"'); 
                                    ?>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <iframe src="<?= base_url('admin/generate_transcript_pdf_preview/' . $students->id) ?>" width="100%" height="700px" style="border: none;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
