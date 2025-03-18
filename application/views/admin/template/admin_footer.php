<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="text-center text-lg-left">
                    Copyright &copy; <?= date('Y'); ?>
                    <a href="https://bmsce.ac.in/" target="_blank">BMSCE</a>. All rights reserved.
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right d-none d-lg-block">
                    Designed and Developed by <a href="https://medhatech.in/" target="_blank" class="text-danger">Medha Tech</a>
                </div>
            </div>
        </div>
    </div>
</footer>

</div>
<!-- end main content -->

</div>
<!-- END layout-wrapper -->

<!-- jQuery  -->
<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/waves.js"></script>
<script src="<?php echo base_url(); ?>assets/js/simplebar.min.js"></script>

<!-- App js -->
<script src="<?php echo base_url(); ?>assets/js/theme.js"></script>

<!-- Bootstrap 5 JS (if needed) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- third party js -->
<script src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/buttons.html5.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/buttons.flash.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/buttons.print.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.keyTable.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/pdfmake.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/vfs_fonts.js"></script>

<!-- Datatables init -->
<script src="<?php echo base_url(); ?>assets/pages/datatables-demo.js"></script>

<!-- Loader Script -->
<script>
    // Hide loader on full page load
    window.addEventListener('load', function () {
        const loader = document.getElementById('global-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    });

    // Optional: Show loader on all AJAX calls
    $(document).ajaxStart(function () {
        $('#global-loader').show();
    }).ajaxStop(function () {
        $('#global-loader').fadeOut('slow');
    });
</script>
<!-- Loader Overlay -->
<div id="loader-overlay">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

</body>
</html>
<script>
    // Show the loader
    function showLoader() {
        document.getElementById('global-loader').style.display = 'flex';
    }

    // Hide the loader
    function hideLoader() {
        document.getElementById('global-loader').style.display = 'none';
    }

    // Example: Show loader when the page is loading (can be triggered on certain events like form submission or AJAX)
    $(window).on('load', function() {
        hideLoader(); // Hide loader when page fully loads
    });

    $(document).on('submit', 'form', function () {
        showLoader();
    });

    // Optional: Show loader on button click like this:
    // $('#yourButtonId').on('click', function () {
    //     showLoader();
    // });
</script>

