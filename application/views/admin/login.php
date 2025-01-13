<div>
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
                                                        <b>BMSCE</b>
                                                    </a>
                                                </div>
                                                <h1 class="h5 mb-1">Welcome Back!</h1>
                                                <p class="text-muted mb-4">Enter your email address and password to access admin panel.</p>
                                                <?php echo form_open($action, 'class="js-validation-signin" method="POST"'); ?>
                                                <?php echo '<span class="text-danger">'.validation_errors().'</span>'; ?>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" placeholder="Enter Username" name="username">
                                                    <div class="input-group-append">
                                                        <div class="input-group-text bg-secondary text-white">@mcehassan.ac.in
                                                            <!-- <i class="fas fa-envelope ml-2"></i> -->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control" id="password-field" name="password"
                                                        placeholder="Enter Password" value="">
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                    
                                                    <button type="submit" class="btn btn-secondary btn-block waves-effect waves-light">Log In</button>
        
                                                <?php echo form_close(); ?>
        
                                                <div class="row mt-4">
                                                    <div class="col-12 text-center">
                                                        <p class="text-muted mb-2"><a href="#" class="text-muted font-weight-medium ml-1">Forgot your password?</a></p>
                                                        <!-- <p class="text-muted mb-0">Don't have an account? <a href="pages-register.html" class="text-muted font-weight-medium ml-1"><b>Sign Up</b></a></p> -->
                                                    </div> <!-- end col -->
                                                </div>
                                                <!-- end row -->
                                            </div> <!-- end .padding-5 -->
                                        </div> <!-- end col -->
                                    </div> <!-- end row -->
                                </div> <!-- end .w-100 -->
                            </div> <!-- end .d-flex -->
                        </div> <!-- end col-->
                    </div> <!-- end row -->
                </div>
                <!-- end container -->
            </div>
            <!-- end page -->