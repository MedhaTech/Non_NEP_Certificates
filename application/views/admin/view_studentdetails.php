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

        </div>
        <div class="card-body">
            <?php echo form_open_multipart($action, 'class="user"'); ?>
            <?php if ($this->session->flashdata('message')) { ?>
            <div align="center" class="alert <?= $this->session->flashdata('status'); ?>" id="msg">
                <?php echo $this->session->flashdata('message') ?>
            </div>
            <?php } ?>
        
            </form>
        </div>
    </div>
</div>

      </div>
    </div>
</div>