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


        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Branch</th>
                                <th>Programme</th>
                                <th>2005</th>
                                <th>2006</th>
                                <th>2007</th>
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
                            $total_2005 = 0;
                            $total_2006 = 0;
                            $total_2007 = 0;
                            $total_2008 = 0;
                            $total_2009 = 0;
                            $total_2010 = 0;
                            $total_2011 = 0;
                            $total_2012 = 0;
                            $total_2013 = 0;
                            $total_2014 = 0;
                            $total_2015 = 0;
                            $total_2016 = 0;
                            $total_2017 = 0;
                            $total_2018 = 0;
                            $total_2019 = 0;
                            $total_2020 = 0;
                            $total_total = 0;
                            foreach ($student_counts as $count):
                                // Initialize total for the current branch
                                $total = 0;

                            ?>
                                <tr>
                                    <td><?php echo $count->branch; ?></td>
                                    <td><?php echo $count->programme; ?></td>
                                    <td>
                                        <?php
                                        echo $count->{'2005'};
                                        $total += $count->{'2005'};
                                        $total_2005 = $total_2005 + $count->{'2005'};
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $count->{'2006'};
                                        $total += $count->{'2006'};
                                        $total_2006 = $total_2006 + $count->{'2006'};
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $count->{'2007'};
                                        $total += $count->{'2007'};
                                        $total_2007 = $total_2007 + $count->{'2007'};
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $count->{'2008'};
                                        $total += $count->{'2008'};
                                        $total_2008 = $total_2008 + $count->{'2008'};
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $count->{'2009'};
                                        $total += $count->{'2009'};
                                        $total_2009 = $total_2009 + $count->{'2009'};
                                        ?>
                                    </td>
                                    <td><?php
                                        echo $count->{'2010'};
                                        $total += $count->{'2010'};
                                        $total_2010 = $total_2010 + $count->{'2010'};
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $count->{'2011'};
                                        $total += $count->{'2011'};
                                        $total_2011 = $total_2011 + $count->{'2011'};
                                        ?>
                                    </td>
                                    <td><?php
                                        echo $count->{'2012'};
                                        $total += $count->{'2012'};
                                        $total_2012 = $total_2012 + $count->{'2012'};
                                        ?>
                                    </td>
                                    <td><?php
                                        echo $count->{'2013'};
                                        $total += $count->{'2013'};
                                        $total_2013 = $total_2013 + $count->{'2013'};
                                        ?>
                                    </td>
                                    <td><?php
                                        echo $count->{'2014'};
                                        $total += $count->{'2014'};
                                        $total_2014 = $total_2014 + $count->{'2014'};
                                        ?>
                                    </td>
                                    <td><?php
                                        echo $count->{'2015'};
                                        $total += $count->{'2015'};
                                        $total_2015 = $total_2015 + $count->{'2015'};
                                        ?>
                                    </td>
                                    <td><?php
                                        echo $count->{'2016'};
                                        $total += $count->{'2016'};
                                        $total_2016 = $total_2016 + $count->{'2016'};
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $count->{'2017'};
                                        $total += $count->{'2017'};
                                        $total_2017 = $total_2017 + $count->{'2017'};
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $count->{'2018'};
                                        $total += $count->{'2018'};
                                        $total_2018 = $total_2018 + $count->{'2018'};
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $count->{'2019'};
                                        $total += $count->{'2019'};
                                        $total_2019 = $total_2019 + $count->{'2019'};
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $count->{'2020'};
                                        $total += $count->{'2020'};
                                        $total_2020 = $total_2020 + $count->{'2020'};
                                        ?>
                                    </td>
                                    <td> <strong> <?php echo $total;
                                                    $total_total = $total_total + $total;
                                                    ?></strong> </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="bg-dark text-light">
                                <td colspan="2" class="text-center">TOTAL</td>
                                <td> <strong> <?php echo $total_2005; ?></strong> </td>
                                <td> <strong> <?php echo $total_2006; ?></strong> </td>
                                <td> <strong> <?php echo $total_2007; ?></strong> </td>
                                <td> <strong> <?php echo $total_2008; ?></strong> </td>
                                <td> <strong> <?php echo $total_2009; ?></strong> </td>
                                <td> <strong> <?php echo $total_2011; ?></strong> </td>
                                <td> <strong> <?php echo $total_2011; ?></strong> </td>
                                <td> <strong> <?php echo $total_2012; ?></strong> </td>
                                <td> <strong> <?php echo $total_2013; ?></strong> </td>
                                <td> <strong> <?php echo $total_2014; ?></strong> </td>
                                <td> <strong> <?php echo $total_2015; ?></strong> </td>
                                <td> <strong> <?php echo $total_2016; ?></strong> </td>
                                <td> <strong> <?php echo $total_2017; ?></strong> </td>
                                <td> <strong> <?php echo $total_2018; ?></strong> </td>
                                <td> <strong> <?php echo $total_2019; ?></strong> </td>
                                <td> <strong> <?php echo $total_2020; ?></strong> </td>
                                <td> <strong> <?php echo $total_total; ?></strong> </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Student Count Table -->

</div> <!-- container-fluid -->
</div>
<!-- End Page-content -->