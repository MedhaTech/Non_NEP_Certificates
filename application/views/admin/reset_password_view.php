<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center min-vh-100">
                <div class="w-100 d-block bg-white shadow-lg rounded my-5">
                    <div class="row">
                        <div class="col-lg-5 d-none d-lg-block bg-login rounded-left"></div>
                        <div class="col-lg-7">
                            <div class="p-5">
                                <div class="text-center mb-5">
                                    <a class="text-dark font-size-22 font-family-secondary">
                                        <b>BMSCE CERTIFY 2008</b>
                                    </a>
                                </div>

                                <h1 class="h5 mb-1">Reset Your Password</h1>
                                <p class="text-muted mb-4">Enter a new password to regain access to your account.</p>

                                <!-- Display Flash Messages -->
                                <?php if ($this->session->flashdata('message')) : ?>
                                    <div class="alert alert-info"><?php echo $this->session->flashdata('message'); ?></div>
                                <?php endif; ?>

                                <form action="<?php echo base_url('admin/update_password'); ?>" method="POST">
                                    <input type="hidden" name="token" value="<?php echo $token; ?>">

                                    <div class="input-group mb-3">
                                        <input type="password" class="form-control" id="password-field" name="password"
                                            placeholder="Enter New Password" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-secondary btn-block waves-effect waves-light">Reset Password</button>
                                </form>

                                <div class="row mt-4">
                                    <div class="col-12 text-center">
                                        <p class="text-muted mb-2">
                                            <a href="<?php echo base_url('admin/'); ?>" class="text-muted font-weight-medium ml-1">
                                                Back to Login
                                            </a>
                                        </p>
                                    </div> 
                                </div>
                            </div> 
                        </div> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
</div>
