<div class="page-content">
    <div class="container-fluid">
              <div class="row">
                 <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 font-size-18"><?= $page_title; ?></h4>
                    </div>
                 </div>
              </div>
              <div class="row m-5">
          <div class="col-md-12 col-sm-12">
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Search Student Details</h6>
        </div>
        <div class="card-body">
            <?php echo form_open_multipart($action, 'class="user"'); ?>
            <?php if ($this->session->flashdata('message')) { ?>
            <div align="center" class="alert <?= $this->session->flashdata('status'); ?>" id="msg">
                <?php echo $this->session->flashdata('message') ?>
            </div>
            <?php } ?>
            <div class="row">
                <div class="form-group col-md-12 col-sm-12">
                    <input type="text" class="form-control" placeholder="Enter USN Number" id="usn"
                        name="usn"
                        value="<?php echo (set_value('usn')) ? set_value('usn') : $usn; ?>">
                    <span class="text-danger"><?php echo form_error('usn'); ?></span>
                </div>
                <div class="form-group col-md-2 col-sm-12">
                    <button class="btn btn-danger btn-md" type="submit">Search</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

      </div>
    </div>
</div>