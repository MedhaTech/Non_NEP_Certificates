<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?? 'My App'; ?></title>

    <!-- Your stylesheets -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">
    
    <!-- Global Loader CSS -->
    <style>
        #global-loader {
            position: fixed;
            z-index: 9999;
            background-color: #ffffff;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-container img {
            width: 80px;
            height: 80px;
        }
    </style>
</head>
<body>

<!-- Global Loader HTML -->
<div id="global-loader">
    <div class="loader-container">
        <img src="<?= base_url('assets/images/loader.gif'); ?>" alt="Loading...">
    </div>
</div>

<!-- Main page content -->
<?= $this->load->view($view_file); ?>

<!-- Scripts -->
<script src="<?= base_url('assets/js/jquery.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>

<!-- Loader JS -->
<script>
    window.addEventListener('load', function () {
        const loader = document.getElementById('global-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    });
</script>

</body>
</html>
