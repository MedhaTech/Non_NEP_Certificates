<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Dashboard</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Student Count Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                     
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Branch</th>
                                    <th>Programme</th>
                                    <th>2008</th>
                                    <th>2009</th>
                                    <th>2010</th>
                                    <th>2011</th>
                                    <th>2012</th>
                                    <th>2013</th>
                                    <th>2014</th>
                                    <th>2015</th>
                                    <th>2016</th>
                                    <th>2017</th>
                                    <th>2018</th>
                                    <th>2019</th>
                                    <th>2020</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($student_counts as $count): 
                                    // Initialize total for the current branch
                                    $total = 0;
                                ?>
                                <tr>
                                    <td><?php echo $count->branch; ?></td>
                                    <td><?php echo $count->programme; ?></td>
                                    <td><?php echo $count->{'2008'}; $total += $count->{'2008'}; ?></td>
                                    <td><?php echo $count->{'2009'}; $total += $count->{'2009'}; ?></td>
                                    <td><?php echo $count->{'2010'}; $total += $count->{'2010'}; ?></td>
                                    <td><?php echo $count->{'2011'}; $total += $count->{'2011'}; ?></td>
                                    <td><?php echo $count->{'2012'}; $total += $count->{'2012'}; ?></td>
                                    <td><?php echo $count->{'2013'}; $total += $count->{'2013'}; ?></td>
                                    <td><?php echo $count->{'2014'}; $total += $count->{'2014'}; ?></td>
                                    <td><?php echo $count->{'2015'}; $total += $count->{'2015'}; ?></td>
                                    <td><?php echo $count->{'2016'}; $total += $count->{'2016'}; ?></td>
                                    <td><?php echo $count->{'2017'}; $total += $count->{'2017'}; ?></td>
                                    <td><?php echo $count->{'2018'}; $total += $count->{'2018'}; ?></td>
                                    <td><?php echo $count->{'2019'}; $total += $count->{'2019'}; ?></td>
                                    <td>  <?php echo $count->{'2020'}; $total += $count->{'2020'}; ?></td>
                                    <td> <strong> <?php echo $total; ?></strong> </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                          
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Student Count Table -->

    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->

<style>
    .thead-dark th {
        background-color: #343a40;
        color: white;
    }
    .table {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f8f9fa;
    }
    .table-bordered {
        border: 1px solid #dee2e6;
    }
</style>