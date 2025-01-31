<div class="page-content">
    <div class="container-fluid">
              <div class="row">
                 <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 font-size-18"><?= $page_title; ?></h4>
                    </div>
                 </div>
              </div>
              <div class="row">
              <div class="col-md-6">
                <?php if ($this->session->flashdata('message')) { ?>
                <div class="alert <?= $this->session->flashdata('status'); ?>" id="msg">
                    <?php echo $this->session->flashdata('message') ?>
                </div>
                <?php } ?>
                <div class="card card-dark shadow">
                    <!-- <div class="card-header">
                        <h3 class="m-1 card-title text-uppercase">Change Password</h6>
                    </div> -->
                    <div class="card-body login-card-body">
                        <?php echo form_open($action, 'class="js-validation-signin" method="POST"'); ?>
                        <div class="form-group">
                            <label>Old Password</label>
                            <input class="form-control" type="password" id="oldpassword"
                                value="<?php echo (set_value('oldpassword')) ? set_value('oldpassword') : $oldpassword; ?>"
                                name="oldpassword" placeholder="Enter Old password" />
                            <span class="text-danger"><?php echo form_error('oldpassword'); ?></span>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input class="form-control" type="password" id="newpassword"
                                value="<?php echo (set_value('newpassword')) ? set_value('newpassword') : $newpassword; ?>"
                                name="newpassword" placeholder="Enter New Password" />
                            <span class="text-danger"><?php echo form_error('newpassword'); ?></span>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input class="form-control" type="password" id="confirmpassword" name="confirmpassword"
                                value="<?php echo (set_value('confirmpassword')) ? set_value('confirmpassword') : $confirmpassword; ?>"
                                placeholder="Enter Confirm Password" />
                            <span class="text-danger"><?php echo form_error('confirmpassword'); ?></span>
                        </div>
                        <div class="col-12 text-right">
                            <button class="btn btn-danger btn-sm" type="submit">Update Paasword</button>
                            <?php echo anchor('admin/dashboard', '<i class="fas fa-arrow-left fa-sm fa-fw"></i> Cancel', 'class="btn btn-dark btn-sm" '); ?>
                        </div>
                        <?php echo form_close(); ?>
               </div>
               </div>
            </div>
    </div>
</div>