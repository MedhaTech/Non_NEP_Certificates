<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page_title; ?> | BMSCE CERTIFY</title>

    <!-- App favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url(); ?>assets/images/BMS_College_of_Engineering.png">

    <!-- App css -->
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/css/theme.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/css/custom.css" rel="stylesheet" type="text/css" />

    <!-- Plugins css -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/responsive.bootstrap4.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/select.bootstrap4.css">

    <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>

    <style>
       #global-loader {
    position: fixed;
    z-index: 9999;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: rgba(0, 0, 0, 0.5); /* Dim the background */
    display: flex;
    align-items: center;
    justify-content: center;
}

.loader-container img {
    width: 70px;
    height: 70px;
}

    </style>
</head>

<body>


    <!-- Begin page -->
    <div id="layout-wrapper">

        <div class="main-content">

            <header id="page-topbar">
                <!-- rest of your header remains unchanged -->

            <div class="navbar-header">
                <!-- LOGO -->
                <div class="navbar-brand-box d-flex align-items-left">
                    <a href="<?php echo base_url(); ?>admin/dashboard" class="logo">
                        <span>
                            BMSCE CERTIFY
                        </span>
                    </a>

                    <button type="button"
                        class="btn btn-sm mr-2 font-size-16 d-lg-none header-item waves-effect waves-light"
                        data-toggle="collapse" data-target="#topnav-menu-content">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                </div>

                <div class="d-flex align-items-center">

                    <div class="dropdown d-inline-block ml-2">
                        <?php echo form_open_multipart('admin/view_studentdetails', 'class="user"'); ?>
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Enter USN"
                                aria-label="Search" id="header-usn" name="usn" aria-describedby="basic-addon2"
                                value="<?php echo (isset($usn) && set_value('usn')) ? set_value('usn') : (isset($usn) ? $usn : ''); ?>" autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>


                    <div class="dropdown d-inline-block ml-2">
                        <button type="button" class="btn header-item waves-effect waves-light"
                            id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <img class="rounded-circle header-profile-user"
                                src="<?php echo base_url(); ?>assets/images/BMS_College_of_Engineering.png"
                                alt="Header Avatar">
                            <span class="d-none d-sm-inline-block ml-1">Welcome <?= $full_name; ?></span>
                            <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                href="<?php echo base_url(); ?>admin/changepassword">
                                <span>Change Password</span>
                            </a>
                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                href="<?php echo base_url(); ?>admin/logout">
                                <span>Log Out</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <div class="topnav">
            <div class="container-fluid">
                <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                    <div class="collapse navbar-collapse" id="topnav-menu-content">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo base_url(); ?>admin/dashboard">
                                    <i class="mdi mdi-view-dashboard mr-2"></i>Dashboard
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo base_url(); ?>admin/courses">
                                    <i class="mdi mdi-google-pages mr-2"></i>Courses
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo base_url(); ?>admin/students">
                                    <i class="nav-icon fas fa-users mr-2"></i>Students
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/backlogs') ?>">
                                    <i class="mdi mdi-file-outline mr-2"></i>Backlogs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/get_grade_card') ?>">
                                    <i class="mdi mdi-file-download-outline mr-2"></i>Grade Card
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/transcript') ?>">
                                    <i class="mdi mdi-file-download-outline mr-2"></i>Transcript
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/pdc') ?>">
                                    <i class="mdi mdi-file-download-outline mr-2"></i>PDC
                                </a>
                            </li>

                        </ul>
                    </div>
                </nav>
            </div>
        </div>

  <!-- Loader Overlay & Popup -->
<div id="loader-overlay">
    <div id="loader-popup">
        <div class="loader-content">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-3 mb-0">Please wait...</p>
        </div>
    </div>
</div>

</body>
<!-- jQuery (required) -->
<!-- jQuery (Required) -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    // Show loader with optional message
    function showLoader(message = 'Please wait...') {
        const $overlay = $('#loader-overlay');
        const $message = $('#loader-popup p');

        if ($overlay.length) {
            $overlay.fadeIn(200);
        }

        if ($message.length && message) {
            $message.text(message);
        }
    }

    // Hide loader
    function hideLoader() {
        const $overlay = $('#loader-overlay');
        if ($overlay.length) {
            $overlay.fadeOut(300);
        }
    }

    // Show loader initially when DOM is ready (optional)
    document.addEventListener("DOMContentLoaded", function () {
        showLoader("Loading page...");
    });

    // Ensure loader is hidden once everything finishes loading
    window.addEventListener("load", function () {
        // Add a slight delay to make transition smooth
        setTimeout(hideLoader, 300);
    });

    // Fallback: Force hide after 5 seconds if something hangs
    setTimeout(() => {
        hideLoader();
    }, 5000);

    // Handle form submissions
    $(document).on('submit', 'form', function () {
        showLoader();
    });

    // AJAX global loader
    $(document).ajaxStart(function () {
        showLoader();
    });

    $(document).ajaxStop(function () {
        hideLoader();
    });
</script>


</html> 