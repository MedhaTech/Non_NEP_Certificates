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
                <?php echo form_open_multipart('admin/get_grade_card_details', 'class="user"'); ?>
                <div class="row">

                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="usn">Enter Student USN</label>
                            <input type="text" class="form-control" id="usn" name="usn" placeholder="Enter USN" autocomplete="off">
                            <div id="usn-error" class="text-danger mt-2"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group mt-4">
                            <button id="generate-btn" type="submit" class="btn btn-primary">Generate Grade Card</button>
                        </div>
                    </div>

                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>