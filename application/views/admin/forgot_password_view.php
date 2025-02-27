
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
                                <h1 class="h5 mb-1">Reset Password</h1>
                                <p class="text-muted mb-4">Enter your email address to receive a password reset link.</p>

                                <?php if ($this->session->flashdata('message')) : ?>
                                    <div class="alert alert-info"><?php echo $this->session->flashdata('message'); ?></div>
                                <?php endif; ?>

                                <form action="<?php echo base_url('admin/forgot_password'); ?>" method="POST">
                                    <div class="form-group">
                                        <label for="email">Enter Your Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <button type="submit" class="btn btn-secondary btn-block">Send Reset Link</button>
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

                            </div> <!-- end .padding-5 -->
                        </div> <!-- end col -->
                    </div> <!-- end row -->
                </div> <!-- end .w-100 -->
            </div> <!-- end .d-flex -->
        </div> <!-- end col-->
    </div> <!-- end row -->
</div>