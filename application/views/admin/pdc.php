<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page_title; ?> | BMSCE CERTIFY</title>
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  
</head>
<body>
<div class="page-content">
<div class="container-fluid">
<div class="card-body" >
<div class="card">
    <div class="container mt-5">
        <h2 class="text-center"><?= $page_title; ?></h2>

        <?php echo form_open('admin/pdc', 'class="form-inline mb-4 justify-content-center"'); ?>
            <div class="form-group">
                <label for="usn" class="mr-2">Enter USN:</label>
                <input type="text" class="form-control" id="usn" name="usn" placeholder="USN" value="<?= set_value('usn'); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary ml-2">Search</button>
        <?php echo form_close(); ?>

        <?php if (isset($students)): ?>
            <div class="card mt-4">
                <div class="card-header">
                    Student Details
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= $students->student_name; ?></h5>
                    <p class="card-text"><strong>USN:</strong> <?= $students->usn; ?></p>
                    <p class="card-text"><strong>CGPA:</strong> 
                        <?php 
                        $semester_data = $this->admin_model->getStudentMarksBySemester($students->usn, 8);
                        $cgpa = !empty($semester_data) ? number_format($semester_data[0]->cgpa ?? 0, 2) : 'N/A';
                        echo $cgpa; 
                        ?>
                    </p>
                    <div class="text-center">
                        <a href="<?php echo base_url('admin/generate_pdc_pdf/' . $students->id); ?>" class="btn btn-primary ml-2">Download PDC</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (validation_errors()): ?>
            <div class="alert alert-danger mt-4">
                <?= validation_errors(); ?>
            </div>
        <?php endif; ?>
    </div>
        </div>
        </div>
        </div>
        </div>
        </div>

    <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>