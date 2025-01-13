
            <!-- jQuery  -->
            <script src="<?php echo base_url();?>assets/js/jquery.min.js"></script>
            <script src="<?php echo base_url();?>assets/js/bootstrap.bundle.min.js"></script>
            <!-- <script src="<?php echo base_url();?>assets/js/metismenu.min.js"></script> -->
            <script src="<?php echo base_url();?>assets/js/waves.js"></script>
            <script src="<?php echo base_url();?>assets/js/simplebar.min.js"></script>
        
            <!-- App js -->
            <script src="<?php echo base_url();?>assets/js/theme.js"></script>
            <script>
$(function() {
    $(".toggle-password").click(function() {

        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
});
</script>
        </body>
        
        </html>