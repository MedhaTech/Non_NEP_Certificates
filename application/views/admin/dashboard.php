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
                <form class="user" id="enquiry_list">
                <div class="row">
                    <!-- Admission Year Input Field -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="admission_year">USN<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="admission_year" placeholder="Enter Student USN">
                            <span class="text-danger"></span>
                        </div>
                    </div>

                    <!-- Semester Dropdown -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="semester">Semester</label>
                            <select class="form-control" id="semester">
                                <option value="1">select</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                            </select>
                            <span class="text-danger"></span>
                        </div>
                    </div>

                    <!-- Year Dropdown -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="year">Select Year</label>
                            <select class="form-control" id="year">
                                <option value="2019even">select</option>
                                <option value="2019even">2019 Even</option>
                                <option value="2019odd">2019 Odd</option>
                                <option value="2020even">2020 Even</option>
                                <option value="2020odd">2020 Odd</option>
                                <option value="2021even">2021 Even</option>
                                <option value="2021odd">2021 Odd</option>
                            </select>
                            <span class="text-danger"></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-block" id="Update">Greade Card</button>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="admission_year">USN<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="admission_year" placeholder="Enter Student USN">
                            <span class="text-danger"></span>
                        </div>
                    </div>
                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-block" id="Update">Transcript</button>
                    </div>
                </div>
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <?php
                           if (isset($students)){
                           if (count($students)) {
                                // Table setup
                                $table_setup = array('table_open' => '<table class="table dt-responsive nowrap table-bordered" border="1" id="basic-datatable">');
                                $this->table->set_template($table_setup);

                                // Table headings
                                $print_fields = array('S.NO', 'USN', 'Student Name', 'Admission Year', 'Programme', 'Branch', 'Action');
                                $this->table->set_heading($print_fields);

                                $i = 1;
                                foreach ($students as $student) {
                                    $edit_url = base_url('admin/editstudent/' . $student->id);  // Adjust URL if necessary
                                    $encryptId = base64_encode($student->id);

                                    $delete_url = base_url('admin/deletestudent/' . $encryptId); // Delete URL with encrypted ID

                                    // Filling table rows dynamically
                                    $result_array = array(
                                        $i++,
                                        anchor('admin/studentdetails/'.$encryptId,  $student->usn),
                                        $student->student_name,
                                        $student->admission_year,
                                        $student->programme,
                                        $student->branch,
                                        "<a href='{$edit_url}' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i> Edit</a> 
                                         <a href='#' class='btn btn-danger btn-sm' onclick='openDeleteModal(\"{$delete_url}\")'><i class='fa fa-trash'></i> Delete</a>"                                    );
                                    $this->table->add_row($result_array);
                                }
                                // Generating and displaying the table
                                echo $this->table->generate();
                            } else {
                                // No data available message
                                echo "<div class='text-center'><img src='" . base_url() . "assets/images/no_data.jpg' class='nodata'><br>No data found</div>";
                               

                            }
                        }
                        ?>
                    </div><!-- end col-->
                </div><!-- end row-->
            </div>
        </div>

                <div class="card">
                    <div class="card-body">
                    <div class="table-responsive">
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
        </div>
        <!-- End Student Count Table -->

    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
