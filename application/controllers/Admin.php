<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->CI = &get_instance();
        $this->load->model('admin_model', '', TRUE);
        $this->load->library(array('table', 'form_validation'));
        $this->load->helper(array('form', 'form_helper'));

        date_default_timezone_set('Asia/Kolkata');
        ini_set('upload_max_filesize', '20M');
    }

    function index()
    {
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|callback_check_database');
        if ($this->form_validation->run() == FALSE) {
            $data['page_title'] = "Admin Login";
            $data['action'] = 'admin';

            $this->login_template->show('admin/login', $data);
        } else {
            $username = $this->input->post('username');
            redirect('admin/dashboard', 'refresh');
        }
    }

    function check_database($password)
    {
        //Field validation succeeded.  Validate against database
        $username = $this->input->post('username');

        //query the database
        $result = $this->admin_model->login($username, md5($password));

        if ($result) {
            $sess_array = array();
            foreach ($result as $row) {
                $sess_array = array(
                    'id' => $row->user_id,
                    'username' => $row->username,
                    'full_name' => $row->full_name,
                    'role' => $row->role
                );
                $this->session->set_userdata('logged_in', $sess_array);
            }
            return TRUE;
        } else {
            $this->form_validation->set_message('check_database', 'Invalid username or password');
            return false;
        }
    }

    function dashboard()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Dashboard";
            $data['menu'] = "dashboard";

            // Fetch student count data
            $data['student_counts'] = $this->admin_model->getStudentCountByYear();

            // Sort the student counts to show UG first
            usort($data['student_counts'], function ($a, $b) {
                // Define a custom order for UG and PG
                $order = ['UG' => 0, 'PG' => 1]; // Adjust based on your actual identifiers
                $aType = isset($order[$a->programme]) ? $order[$a->programme] : 2; // Default to 2 if not found
                $bType = isset($order[$b->programme]) ? $order[$b->programme] : 2; // Default to 2 if not found
                return $aType - $bType; // Sort by type
            });

            $this->admin_template->show('admin/dashboard', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    function logout()
    {
        $this->session->unset_userdata('logged_in');
        session_destroy();
        redirect('admin', 'refresh');
    }

    function changepassword()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Change Password";
            $data['menu'] = "changepassword";

            $this->form_validation->set_rules('oldpassword', 'Old Password', 'required');
            $this->form_validation->set_rules('newpassword', 'New Password', 'required');
            $this->form_validation->set_rules('confirmpassword', 'Confirm Password', 'required|matches[newpassword]');

            if ($this->form_validation->run() === FALSE) {

                $data['action'] = 'admin/changepassword/' . $data['id'];
                $this->admin_template->show('admin/changepassword', $data);
            } else {
                $oldpassword = $this->input->post('oldpassword');
                $newpassword = $this->input->post('newpassword');
                $confirmpassword = $this->input->post('confirmpassword');

                if ($oldpassword == $newpassword) {
                    $this->session->set_flashdata('message', 'Old and New Password should not be same...!');
                    $this->session->set_flashdata('status', 'alert-warning');
                } else {
                    $updateDetails = array('password' => md5($newpassword));
                    $result = $this->admin_model->AdminChangePassword($data['id'], $oldpassword, $updateDetails, 'users');
                    if ($result) {
                        $this->session->set_flashdata('message', 'Password udpated successfully...!');
                        $this->session->set_flashdata('status', 'alert-success');
                    } else {
                        $this->session->set_flashdata('message', 'Oops something went wrong please try again.!');
                        $this->session->set_flashdata('status', 'alert-warning');
                    }
                }
                redirect('/admin/changepassword', 'refresh');
            }
        } else {
            redirect('admin', 'refresh');
        }
    }

    public function courses()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];
    
            $data['page_title'] = "Courses";
            $data['menu'] = "students";
    
            $data['students'] = $this->admin_model->getDetails('courses', null)->result();
            $data['programmes'] = $this->admin_model->getDistinctValues('programme', 'courses');
            $data['branches'] = $this->admin_model->getDistinctValues('branch', 'courses');
            $data['semesters'] = $this->admin_model->getDistinctValues('semester', 'courses');
    
            $this->admin_template->show('admin/courses', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }
    
    public function filterCourses()
{
    $programme = $this->input->post('programme');
    $branch = $this->input->post('branch');
    $semester = $this->input->post('semester');

    $this->db->select('*');
    $this->db->from('courses');

    if (!empty($programme)) {
        $this->db->where('programme', $programme);
    }
    if (!empty($branch)) {
        $this->db->where('branch', $branch);
    }
    if (!empty($semester)) {
        $this->db->where('semester', $semester);
    }

    $query = $this->db->get();
    $courses = $query->result();

    $output = "";
    $i = 1;
    foreach ($courses as $course) {
        $edit_url = base_url('admin/editcourse/' . $course->id);
        $encryptId = base64_encode($course->id);
        $delete_url = base_url('admin/deleteCourse/' . $encryptId);

        $output .= "<tr>
            <td>{$i}</td>
            <td><a href='".base_url('admin/viewcourseDetails/'.$encryptId)."'>{$course->course_code}</a></td>
            <td>{$course->course_name}</td>
            <td>{$course->branch}</td>
            <td>
                <a href='{$edit_url}' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i> Edit</a>
                <a href='{$delete_url}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this course?\")'><i class='fa fa-trash'></i> Delete</a>
            </td>
            <td>{$course->semester}</td>
        </tr>";

        $i++;
    }

    echo $output;
}


    public function add_newcourse()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];
            $data['page_title'] = "Add New Course";
            $data['menu'] = "newcourse";
            $data['programme_options'] = array(" " => "Select Year") + $this->globals->programme();
            $data['branch_options'] = array(" " => "Select Branch") + $this->globals->branch();
            $data['semester_options'] = array(" " => "Select Semester") + $this->globals->semester();
            $data['year_options'] = array(" " => "Select Year") + $this->globals->year();

            // Set validation rules
            $this->form_validation->set_rules('course_code', 'Course Code', 'required');
            $this->form_validation->set_rules('course_name', 'Course Name', 'required');
            $this->form_validation->set_rules('programme', 'Programme', 'required');
            $this->form_validation->set_rules('branch', 'Branch', 'required');
            $this->form_validation->set_rules('semester', 'Semester', 'required');
            $this->form_validation->set_rules('year', 'Year', 'required');
            $this->form_validation->set_rules('crhrs', 'Course Hourse');
            $this->form_validation->set_rules('course_order', 'Course Order');

            if ($this->form_validation->run() === FALSE) {
                $data['action'] = 'admin/add_newcourse';
                $this->admin_template->show('admin/add_newcourse', $data);
            } else {
                // Prepare data to insert
                $insertDetails = array(
                    'course_code' => $this->input->post('course_code'),
                    'course_name' => $this->input->post('course_name'),
                    'programme' => $this->input->post('programme'),
                    'branch' => $this->input->post('branch'),
                    'semester' => $this->input->post('semester'),
                    'year' => strtolower($this->input->post('year')),
                    'crhrs' => $this->input->post('crhrs'),
                    'course_order' => $this->input->post('course_order'),
                );

                // Insert the data into the 'students' table
                $result = $this->admin_model->insertDetails('courses', $insertDetails);

                // Set success or failure message
                if ($result) {
                    $this->session->set_flashdata('message', 'Course details have been successfully updated!');
                    $this->session->set_flashdata('status', 'alert-success');
                } else {
                    $this->session->set_flashdata('message', 'Oops! Something went wrong, please try again.');
                    $this->session->set_flashdata('status', 'alert-warning');
                }

                // Redirect to the same page after processing
                redirect('admin/courses', 'refresh');
            }
        } else {
            // Redirect if the user is not logged in
            redirect('admin', 'refresh');
        }
    }

    public function editcourse($id)
    {
        // Check if the user is logged in
        if ($this->session->userdata('logged_in')) {
            // Get session data
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];
            $data['page_title'] = "Edit Course";
            $data['menu'] = "editcourse";
            $data['programme_options'] = array(" " => "Select Year") + $this->globals->programme();
            $data['branch_options'] = array(" " => "Select Branch") + $this->globals->branch();
            $data['semester_options'] = array(" " => "Select Semester") + $this->globals->semester();
            $data['year_options'] = array(" " => "Select Year") + $this->globals->year();

            // Get the current course details using the provided ID
            $data['admissionDetails'] = $this->admin_model->getDetails('courses', $id)->row();

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            // Set form validation rules
            $this->form_validation->set_rules('course_code', 'Course Code', 'required');
            $this->form_validation->set_rules('course_name', 'Course Name', 'required');
            $this->form_validation->set_rules('programme', 'Programme', 'required');
            $this->form_validation->set_rules('branch', 'Branch', 'required');
            $this->form_validation->set_rules('semester', 'Semester', 'required');
            $this->form_validation->set_rules('year', 'Year', 'required');
            $this->form_validation->set_rules('crhrs', 'Course Hours');
            $this->form_validation->set_rules('course_order', 'Course Order');

            // If form validation fails
            if ($this->form_validation->run() === FALSE) {
                // Show form again if validation fails
                $this->admin_template->show('admin/editcourse', $data);
            } else {
                // If form validation passed, prepare data for update
                $updateDetails = array(
                    'course_code' => $this->input->post('course_code'),
                    'course_name' => $this->input->post('course_name'),
                    'programme' => $this->input->post('programme'),
                    'branch' => $this->input->post('branch'),
                    'semester' => $this->input->post('semester'),
                    'year' => $this->input->post('year'),
                    'crhrs' => $this->input->post('crhrs'),
                    'course_order' => $this->input->post('course_order'),
                );

                // Call model to update the course details
                $result = $this->admin_model->updateDetails1($id, $updateDetails, 'courses');

                // Flash message for successful or failed update
                if ($result) {
                    $this->session->set_flashdata('message', 'Course details updated successfully!');
                    $this->session->set_flashdata('status', 'alert-success');
                } else {
                    $this->session->set_flashdata('message', 'Something went wrong, please try again!');
                    $this->session->set_flashdata('status', 'alert-danger');
                }

                // Redirect to the same page to refresh the form
                redirect('admin/courses/' . $id);
            }
        } else {
            // Redirect to the login page if user is not logged in
            redirect('admin');
        }
    }

    public function deleteCourse($id)
    {
        // Check if the user is logged in
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];
            // Decode the ID (since you base64-encoded it for security)
            $decoded_id = base64_decode($id);

            $result = $this->admin_model->deleteCourse($decoded_id);

            if ($result) {
                $this->session->set_flashdata('message', 'Course deleted successfully!');
                $this->session->set_flashdata('status', 'alert-success');
            } else {
                $this->session->set_flashdata('message', 'Something went wrong, please try again!');
                $this->session->set_flashdata('status', 'alert-danger');
            }

            // Redirect to the course list page
            redirect('admin/courses');
        } else {
            // Redirect to the login page if not logged in
            redirect('admin');
        }
    }

    public function viewcourseDetails($id)
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Course Details";
            $data['menu'] = "students";

            $decoded_id = base64_decode($id);

            // Fetch course details for the given course ID
            $data['students'] = $this->admin_model->getDetails('courses', $decoded_id)->row(); // Using row() for a single course

            $this->admin_template->show('admin/view_coursedetails', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    // public function students()
    // {
    // 	if ($this->session->userdata('logged_in')) {
    // 		$session_data = $this->session->userdata('logged_in');
    // 		$data['id'] = $session_data['id'];
    // 		$data['username'] = $session_data['username'];
    // 		$data['full_name'] = $session_data['full_name'];
    // 		$data['role'] = $session_data['role'];

    // 		$data['page_title'] = "Students";
    // 		$data['menu'] = "students";

    // 		$data['students'] = $this->admin_model->getDetails('students', $id)->result();

    // 		$this->admin_template->show('admin/students', $data);
    // 	} else {
    // 		redirect('admin', 'refresh');
    // 	}
    // }

    // public function students()
    // {
    //     if ($this->session->userdata('logged_in')) {
    //         // Retrieve session data
    //         $session_data = $this->session->userdata('logged_in');
    //         $data['id'] = $session_data['id'];
    //         $data['username'] = $session_data['username'];
    //         $data['full_name'] = $session_data['full_name'];
    //         $data['role'] = $session_data['role'];

    //         $data['page_title'] = "Students";
    //         $data['menu'] = "students";

    //         // Adding "All" options for filters
    //         $data['admission_options'] = array("All Admission Years" => "All Admission Years") + $this->globals->admissionyear();
    //         $data['programme_options'] = array("All Programmes" => "All Programmes") + $this->globals->programme();
    //         $data['branch_options'] = array("All Branches" => "All Branches") + $this->globals->branch();

    //         // Get selected filters from the URL
    //         $admission_year = $this->input->get('admission_year');
    //         $programme = $this->input->get('programme');
    //         $branch = $this->input->get('branch');

    //         // Initialize filter conditions
    //         $filter_conditions = [];

    //         // Apply filter conditions if selected (only non-'All' values)
    //         if ($admission_year && $admission_year !== 'All Admission Years') {
    //             $filter_conditions['admission_year'] = $admission_year;
    //         }
    //         if ($programme && $programme !== 'All Programmes') {
    //             $filter_conditions['programme'] = $programme;
    //         }
    //         if ($branch && $branch !== 'All Branches') {
    //             $filter_conditions['branch'] = $branch;
    //         }

    //         // If no filter is set (all are "All"), fetch all students
    //         if (empty($filter_conditions)) {
    //             // When no filter is set, we should show all students
    //             $data['students'] = $this->admin_model->getstudentDetail('students')->result();
    //         } else {
    //             // Get the filtered student list
    //             $data['students'] = $this->admin_model->getstudentDetail('students', $filter_conditions)->result();
    //         }

    //         // Pass selected filter values to the view
    //         $data['selected_admission_year'] = $admission_year;
    //         $data['selected_programme'] = $programme;
    //         $data['selected_branch'] = $branch;

    //         // Render the view
    //         $this->admin_template->show('admin/students', $data);
    //     } else {
    //         redirect('admin', 'refresh');
    //     }
    // }

    public function students()
    {
        if ($this->session->userdata('logged_in')) {
            // Retrieve session data
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Students";
            $data['menu'] = "students";

            // Adding "All" options for filters
            $data['admission_options'] = array("0" => "Select Admission Year", "All" => "All Admission Years") + $this->globals->admissionyear();
            $data['programme_options'] = array("0" => "Select Programmes", "All" => "All Programmes") + $this->globals->programme();
            $data['branch_options'] = array("0" => "Select Branches", "All" => "All Branches") + $this->globals->branch();

            // Get selected filters from the POST data
            $admission_year = $this->input->post('admission_year');
            $programme = $this->input->post('programme');
            $branch = $this->input->post('branch');

            // Initialize filter conditions
            $filter_conditions = [];

            // Apply filter conditions only if non-"All" values are selected
            if ($admission_year && $admission_year !== '0' && $admission_year !== 'All') {
                $filter_conditions['admission_year'] = $admission_year;
            }

            if ($programme && $programme !== '0' && $programme !== 'All') {
                $filter_conditions['programme'] = $programme;
            }

            if ($branch && $branch !== '0' && $branch !== 'All') {
                $filter_conditions['branch'] = $branch;
            }

            // Fetch all student data when no filter is applied (either "All" or default value is selected)
            if (!empty($filter_conditions)) {
                // Fetch filtered student list
                $data['students'] = $this->admin_model->getstudentDetail('students', $filter_conditions)->result();
            } else {
                // If no filter is applied (i.e., page is visited for the first time or no filter is selected), set 'students' to an empty array.
                $data['students'] = [];  // This ensures no data is shown by default
            }

            // Pass selected filter values to the view
            $data['selected_admission_year'] = $admission_year;
            $data['selected_programme'] = $programme;
            $data['selected_branch'] = $branch;

            // Render the view
            $this->admin_template->show('admin/students', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }


    public function add_newstudent()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];
            $data['page_title'] = "Add New Student";
            $data['menu'] = "newstudent";
            $data['admission_options'] = array(" " => "Select Admission Year") + $this->globals->admissionyear();
            $data['programme_options'] = array(" " => "Select Programme") + $this->globals->programme();
            $data['branch_options'] = array(" " => "Select Branch") + $this->globals->branch();
            $data['gender_options'] = array(" " => "Select Gender") + $this->globals->gender();

            // Set validation rules
            $this->form_validation->set_rules('usn', 'Usn', 'required');
            $this->form_validation->set_rules('student_name', 'Student Name', 'required');
            $this->form_validation->set_rules('admission_year', 'Admission Year', 'required');
            $this->form_validation->set_rules('programme', 'Programme', 'required');
            $this->form_validation->set_rules('branch', 'Branch', 'required');
            $this->form_validation->set_rules('date_of_birth', 'Date of Birth', 'required');
            $this->form_validation->set_rules('gender', 'Gender', 'required');
            $this->form_validation->set_rules('category', 'Category', 'required');
            $this->form_validation->set_rules('mobile', 'Mobile', 'required');
            $this->form_validation->set_rules('parent_mobile', 'Parent Mobile', 'required');
            $this->form_validation->set_rules('father_name', 'Father Name', 'required');
            $this->form_validation->set_rules('mother_name', 'Mother Name', 'required');

            if ($this->form_validation->run() === FALSE) {
                $data['action'] = 'admin/add_newstudent';
                $this->admin_template->show('admin/add_newstudent', $data);
            } else {
                // Prepare data to insert
                $insertDetails = array(
                    'usn' => $this->input->post('usn'),
                    'student_name' => $this->input->post('student_name'),
                    'admission_year' => $this->input->post('admission_year'),
                    'programme' => $this->input->post('programme'),
                    'branch' => $this->input->post('branch'),
                    'date_of_birth' => $this->input->post('date_of_birth'),
                    'gender' => strtolower($this->input->post('gender')),
                    'category' => $this->input->post('category'),
                    'mobile' => $this->input->post('mobile'),
                    'parent_mobile' => $this->input->post('parent_mobile'),
                    'father_name' => $this->input->post('father_name'),
                    'mother_name' => $this->input->post('mother_name'),
                );

                // Insert the data into the 'students' table
                $result = $this->admin_model->insertDetails('students', $insertDetails);

                // Set success or failure message
                if ($result) {
                    $this->session->set_flashdata('message', 'Student details have been successfully updated!');
                    $this->session->set_flashdata('status', 'alert-success');
                } else {
                    $this->session->set_flashdata('message', 'Oops! Something went wrong, please try again.');
                    $this->session->set_flashdata('status', 'alert-warning');
                }

                // Redirect to the same page after processing
                redirect('admin/students', 'refresh');
            }
        } else {
            // Redirect if the user is not logged in
            redirect('admin', 'refresh');
        }
    }

    public function editstudent($id)
    {
        // Check if the user is logged in
        if ($this->session->userdata('logged_in')) {
            // Get session data
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];
            $data['page_title'] = "Edit Student";
            $data['menu'] = "editstudent";
            $data['admission_options'] = array(" " => "Select Admission Year") + $this->globals->admissionyear();
            $data['programme_options'] = array(" " => "Select Programme") + $this->globals->programme();
            $data['gender_options'] = array(" " => "Select Gender") + $this->globals->gender();
            $data['branch_options'] = array(" " => "Select Branch") + $this->globals->branch();

            // Get the current course details using the provided ID
            $data['admissionDetails'] = $this->admin_model->getDetails('students', $id)->row();

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            // Set validation rules
            $this->form_validation->set_rules('usn', 'Usn', 'required');
            $this->form_validation->set_rules('student_name', 'Student Name', 'required');
            $this->form_validation->set_rules('admission_year', 'Admission Year', 'required');
            $this->form_validation->set_rules('programme', 'Programme', 'required');
            $this->form_validation->set_rules('branch', 'Branch', 'required');
            $this->form_validation->set_rules('date_of_birth', 'Date of Birth', 'required');
            $this->form_validation->set_rules('gender', 'Gender', 'required');
            $this->form_validation->set_rules('category', 'Category', 'required');
            $this->form_validation->set_rules('mobile', 'Mobile', 'required');
            $this->form_validation->set_rules('parent_mobile', 'Parent Mobile', 'required');
            $this->form_validation->set_rules('father_name', 'Father Name', 'required');
            $this->form_validation->set_rules('mother_name', 'Mother Name', 'required');

            // If form validation fails
            if ($this->form_validation->run() === FALSE) {
                // Show form again if validation fails
                $this->admin_template->show('admin/editstudent', $data);
            } else {
                // If form validation passed, prepare data for update
                $updateDetails = array(
                    'usn' => $this->input->post('usn'),
                    'student_name' => $this->input->post('student_name'),
                    'admission_year' => $this->input->post('admission_year'),
                    'programme' => $this->input->post('programme'),
                    'branch' => $this->input->post('branch'),
                    'date_of_birth' => $this->input->post('date_of_birth'),
                    'gender' => strtolower($this->input->post('gender')),
                    'category' => $this->input->post('category'),
                    'mobile' => $this->input->post('mobile'),
                    'parent_mobile' => $this->input->post('parent_mobile'),
                    'father_name' => $this->input->post('father_name'),
                    'mother_name' => $this->input->post('mother_name'),
                );

                $result = $this->admin_model->updateDetails1($id, $updateDetails, 'students');

                if ($result) {
                    $this->session->set_flashdata('message', 'Student details updated successfully!');
                    $this->session->set_flashdata('status', 'alert-success');
                } else {
                    $this->session->set_flashdata('message', 'Something went wrong, please try again!');
                    $this->session->set_flashdata('status', 'alert-danger');
                }

                // Redirect to the same page to refresh the form
                redirect('admin/students/' . $id);
            }
        } else {
            redirect('admin');
        }
    }

    public function deletestudent($id)
    {
        // Check if the user is logged in
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];
            // Decode the ID (since you base64-encoded it for security)
            $decoded_id = base64_decode($id);

            $result = $this->admin_model->deleteStudent($decoded_id);

            if ($result) {
                $this->session->set_flashdata('message', 'Student deleted successfully!');
                $this->session->set_flashdata('status', 'alert-success');
            } else {
                $this->session->set_flashdata('message', 'Something went wrong, please try again!');
                $this->session->set_flashdata('status', 'alert-danger');
            }

            // Redirect to the course list page
            redirect('admin/students');
        } else {
            // Redirect to the login page if not logged in
            redirect('admin');
        }
    }

    public function viewstudentDetails($id)
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Student Details";
            $data['menu'] = "students";

            $decoded_id = base64_decode($id);

            // Fetch course details for the given course ID
            $data['students'] = $this->admin_model->getDetails('students', $decoded_id)->row(); // Using row() for a single course

            $this->admin_template->show('admin/view_studentdetails', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    public function view_studentdetails()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Student Details";
            $data['menu'] = "students";

            // If form validation fails
            $this->form_validation->set_rules('usn', 'USN', 'required');
            if ($this->form_validation->run() === FALSE) {
                $data['action'] = 'admin/view_studentdetails';
                $this->admin_template->show('admin/view_studentdetails', $data);
            } else {
                // If form is valid, look for the USN and fetch details
                $usn = $this->input->post('usn');
                $details = $this->admin_model->getDetailsbyfield($usn, 'usn', 'students')->row();
                if ($details) {
                    $id = $details->id;
                    $encryptId = base64_encode($id);
                    // Check if the redirection path is valid
                    redirect('admin/studentdetails/' . $encryptId, 'refresh');
                } else {
                    redirect('admin/students', 'refresh');
                }
            }
        } else {
            redirect('admin/timeout');
        }
    }

    // public function studentdetails($encryptId)
    // {
    // 	if ($this->session->userdata('logged_in')) {
    //         $session_data = $this->session->userdata('logged_in');
    //         $data['id'] = $session_data['id'];
    //         $data['username'] = $session_data['username'];
    //         $data['full_name'] = $session_data['full_name'];
    //         $data['role'] = $session_data['role'];

    //         $data['page_title'] = "Student Details";
    //         $data['menu'] = "students";

    // 		$data['encryptId'] = $encryptId;
    // 		$id = base64_decode($encryptId);
    // 		// $data['student'] = $id;

    // 		$data['students'] = $this->admin_model->getDetails('students', $id)->row();

    // 		$this->admin_template->show('admin/studentdetails', $data);
    // 	} else {
    // 		redirect('admin/timeout');
    // 	}
    // }

    public function studentdetails($encryptId)
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Student Details";
            $data['menu'] = "students";

            $data['encryptId'] = $encryptId;
            $id = base64_decode($encryptId);

            // Fetch student details
            $data['students'] = $this->admin_model->getDetails('students', $id)->row();
            $data['usn'] = $data['students']->usn;
            $data['studentmarks'] = $this->admin_model->getDetails('students_marks', $id)->row();

            // var_dump($data['studentmarks']); die();


            // Fetch marks and course data for each semester (1 to 8)
            for ($semester = 1; $semester <= 8; $semester++) {
                $data["semester_$semester"] = $this->admin_model->getStudentMarksBySemester($data['students']->usn, $semester);
            }

            // Show the view
            $this->admin_template->show('admin/studentdetails', $data);
        } else {
            redirect('admin/timeout');
        }
    }

    public function save_marks()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Student Details";
            $data['menu'] = "students";

            $data['studentmarks'] = $this->admin_model->getDetails('students_marks', $id)->row();
            // var_dump($data['studentmarks']); die();

            // Get POST data from the form
            $course_code = $this->input->post('course_code');  // Assuming course_code is being passed
            $course_name = $this->input->post('course_name');
            $cie = $this->input->post('cie');
            $see = $this->input->post('see');
            $cie_see = $this->input->post('cie_see');
            $grade = $this->input->post('grade');
            $sgpa = $this->input->post('sgpa');
            $cgpa = $this->input->post('cgpa');
            $semester = $this->input->post('semester');
            $grade_points = $this->input->post('grade_points');
            $credits_earned = $this->input->post('credits_earned');
            $credits_actual = $this->input->post('credits_actual');
            $ci = $this->input->post('ci');
            $suborder = $this->input->post('suborder');
            $reexamyear = $this->input->post('reexamyear');
            $result_year = $this->input->post('result_year');
            $exam_period = $this->input->post('exam_period');
            $barcode = $this->input->post('barcode');
            $torder = $this->input->post('torder');
            $texam_period = $this->input->post('texam_period');
            $usn = $this->input->post('stu_usn'); // Get student unique identifier (usn)

            // Check if the course_code exists in the courses table
            $this->db->select('*');
            $this->db->from('courses');
            $this->db->where('course_code', $course_code);
            $courseExists = $this->db->get()->row();

            if ($courseExists) {
                // Prepare the update data for students_marks table
                $updateDetails = array(
                    'course_code' => $course_code,
                    'course_name' => $course_name,
                    'cie' => $cie,
                    'see' => $see,
                    'cie_see' => $cie_see,
                    'grade' => $grade,
                    'sgpa' => $sgpa,
                    'cgpa' => $cgpa,
                    'semester' => $semester,
                    'grade_points' => $grade_points,
                    'credits_earned' => $credits_earned,
                    'credits_actual' => $credits_actual,
                    'ci' => $ci,
                    'suborder' => $suborder,
                    'reexamyear' => $reexamyear,
                    'result_year' => $result_year,
                    'exam_period' => $exam_period,
                    'barcode' => $barcode,
                    'torder' => $torder,
                    'texam_period' => $texam_period,
                );

                // Check if the student marks for this course exists before updating
                $this->db->select('*');
                $this->db->from('students_marks');
                $this->db->where('usn', $usn);
                +$this->db->where('course_code', $course_code);  // Use the correct column name
                $existingRecord = $this->db->get()->row();

                if ($existingRecord) {
                    // Record exists, so update it
                    $this->db->where('usn', $usn);
                    $this->db->where('course_code', $course_code);  // Use the correct column name
                    $this->db->update('students_marks', $updateDetails);

                    if ($this->db->affected_rows() > 0) {
                        // Update successful
                        $this->session->set_flashdata('message', 'Marks updated successfully!');
                        $this->session->set_flashdata('status', 'alert-success');
                    } else {
                        // No rows affected (maybe no changes were made)
                        $this->session->set_flashdata('message', 'No changes were made!');
                        $this->session->set_flashdata('status', 'alert-warning');
                    }
                } else {
                    // Record does not exist, maybe an invalid usn or course
                    $this->session->set_flashdata('message', 'No matching record found for the given student and course!');
                    $this->session->set_flashdata('status', 'alert-danger');
                }
            } else {
                // Course code does not exist in the courses table
                $this->session->set_flashdata('message', 'Invalid course code!');
                $this->session->set_flashdata('status', 'alert-danger');
            }

            // Redirect back to the student's page
            redirect('admin/students/' . $data['id']);
        } else {
            // If the user is not logged in, redirect to the login page
            redirect('admin');
        }
    }
    public function generate_student_pdf($id, $semester)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        if ($this->session->userdata('logged_in')) {
            $student = $this->admin_model->getDetails('students', $id)->row();

            if (!$student) {
                show_404();
            }

            if (ob_get_contents()) ob_end_clean();

            require_once APPPATH . 'libraries/ReportPDF.php';
            require_once APPPATH . '../vendor/autoload.php';
            $pdf = new ReportPDF('L', 'mm', 'A4');
            $pdf->AddPage();

            // Background and student details
            $pdf->Image(base_url('assets/images/certificate_bg.png'), 0, 0, 297, 210);

            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);

            $pdf->SetXY(66, 72);
            $pdf->Cell(50, 10, $student->usn, 0, 1);

            $pdf->SetXY(61, 56);
            $pdf->Cell(50, 10, $student->student_name, 0, 1);

            $pdf->SetXY(60, 61.3);
            $pdf->Cell(50, 10, $student->mother_name, 0, 1);

            $pdf->SetXY(60, 66.5);
            $pdf->Cell(50, 10, $student->father_name, 0, 1);

            $pdf->SetXY(201, 61.2);
            $pdf->Cell(50, 10, $semester, 0, 1);

            // Get semester data
            $semester_data = $this->admin_model->getStudentMarksBySemester($student->usn, $semester);

            // Define fixed parameters
            $table_start_y = 85;
            $max_table_height = 64;
            $header_height = 8;

            // Calculate row height based on number of rows
            $num_rows = count($semester_data);
            $available_height = $max_table_height - $header_height;
            $row_height = ($available_height - 5) / max($num_rows, 1); // Adjust height


            // Table Headers
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetTextColor(231, 119, 22);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetXY(30, $table_start_y);
            $pdf->Cell(15, $header_height, 'Sl.No.', 1, 0, 'C', true);
            $pdf->Cell(40, $header_height, 'Course Code', 1, 0, 'C', true);
            $pdf->Cell(80, $header_height, 'Course Title', 1, 0, 'C', true);
            $pdf->Cell(25, $header_height, 'Credits', 1, 0, 'C', true);
            $pdf->Cell(40, $header_height, 'Grade Awarded', 1, 0, 'C', true);
            $pdf->Cell(40, $header_height, 'Grade Points', 1, 1, 'C', true);

            // Add data rows and calculate totals
            if (!empty($semester_data)) {
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                $sno = 1;
                $y = $table_start_y + $header_height;

                $total_credits_actual = 0;
                $total_credits_earned = 0;

                foreach ($semester_data as $course) {
                    $pdf->SetXY(30, $y);
                    $pdf->Cell(15, $row_height, $sno++, 1, 0, 'C', true);
                    $pdf->Cell(40, $row_height, $course->course_code, 1, 0, 'C', true);
                    $pdf->Cell(80, $row_height, $course->course_name, 1, 0, 'L', true);
                    $pdf->Cell(25, $row_height, $course->credits_earned, 1, 0, 'C', true);
                    $pdf->Cell(40, $row_height, $course->grade, 1, 0, 'C', true);
                    $pdf->Cell(40, $row_height, $course->grade_points, 1, 1, 'C', true);
                    $y += $row_height;

                    // Add to totals
                    $total_credits_actual += $course->credits_actual;
                    $total_credits_earned += $course->credits_earned;
                    if (!empty($course->barcode)) {
                        $barcode_number = $course->barcode;
                    }
                }

                // Add SGPA row at the bottom of the table
                $pdf->SetXY(155, 176);
                $pdf->Cell(50, 10, $semester_data[0]->sgpa, 0, 1);


                $pdf->SetXY(155, 164);
                $pdf->Cell(50, 10, $total_credits_actual, 0, 1);


                $pdf->SetXY(155, 170);
                $pdf->Cell(50, 10, $total_credits_earned, 0, 1);

                $pdf->SetAutoPageBreak(false); // Disable automatic page breaks
                $pdf->SetMargins(0, 0, 0); // Remove margins

                // Force Y position to the bottom while ensuring visibility
                $bottom_y = min(188, $pdf->GetPageHeight() - 15);
                $pdf->SetXY(155, $bottom_y);
                $pdf->Cell(50, 10, $semester_data[0]->cgpa);


                $bottom_y = min(188, $pdf->GetPageHeight() - 15);
                $pdf->SetXY(78, $bottom_y);
                $pdf->Cell(50, 10, date('d-M-Y'));




                $bottom_y = min(182, $pdf->GetPageHeight() - 15);
                $pdf->SetXY(155, $bottom_y);
                $pdf->Cell(50, 10, $total_credits_earned);

                // If needed, adjust background image placement
                $pdf->SetFont('Times', 'B', 10);
                $pdf->SetXY(201, 67.2);
                $pdf->Cell(50, 10, date('F Y', strtotime($course->result_year)), 0, 1);





                // Generate Barcode Below CGPA
                if (!empty($barcode_number)) {
                    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                    $barcode = $generator->getBarcode($barcode_number, $generator::TYPE_CODE_128);

                    // Save barcode image temporarily
                    $barcodePath = APPPATH . 'temp/barcode_' . $barcode_number . '.png';
                    file_put_contents($barcodePath, $barcode);

                    // Add barcode image **below CGPA**
                    $pdf->Image($barcodePath, 19, 174, 50, 10); // Adjusted position
                }
            }

            // $pdf->Output();
            $pdf->Output('D', $semester . ' semester Grade Card' . '.pdf');
        } else {
            redirect('admin/timeout');
        }
    }
    public function generate_transcript_pdf($id)
    {
        if ($this->session->userdata('logged_in')) {
            $student = $this->admin_model->getDetails('students', $id)->row();

            if (!$student) {
                show_404();
            }

            if (ob_get_contents()) ob_end_clean();

            require_once APPPATH . 'libraries/ReportPDF.php';
            $pdf = new ReportPDF('P', 'mm', 'A4');
            $pdf->AddPage();

            // Background Image
            $pdf->Image(base_url('assets/images/transcript.png'), 0, 0, 210, 297);
            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);

            // Student Info at the top
            $pdf->SetXY(60, 31.5);
            $pdf->Cell(50, 6, $student->student_name);
            $pdf->SetXY(60, 37);
            $pdf->Cell(50, 6, $student->usn);
            $pdf->SetXY(60, 42);
            $pdf->Cell(50, 6, $student->admission_year);

            $pdf->SetXY(60, 52);
            $pdf->Cell(50, 6, $student->programme);
            $pdf->SetXY(33, 58.8);
            $pdf->Cell(50, 6, $student->branch);

            // Layout Variables
            $y_position = 91;
            $left_x = 10;
            $right_x = 105;
            $table_width = 90.5;

            for ($semester = 1; $semester <= 8; $semester++) {
                $semester_data = $this->admin_model->getStudentMarksBySemester($student->usn, $semester);
                $current_x = ($semester % 2 == 1) ? $left_x : $right_x;

                if (!empty($semester_data)) {
                    $exam_period = $semester_data[0]->exam_period ?? 'N/A';
                } else {
                    $exam_period = 'N/A';
                }

                // Display Semester and Exam Period
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetXY($current_x, $y_position - 3);
                $pdf->Cell($table_width, 5, "Semester: $semester  |  Exam Period: $exam_period", 1, 0, 'L');

                // Data Rows
                $pdf->SetFont('Arial', '', 7);
                $row_y = $y_position + 2;
                $count = 1;
                foreach ($semester_data as $course) {
                    if ($row_y > 270) {
                        $pdf->AddPage();
                        $pdf->SetXY($current_x, 20);
                        $row_y = 25;
                    }
                    $pdf->SetXY($current_x, $row_y);
                    $pdf->Cell(3, 5, $count++, 1, 0, 'C');
                    $pdf->Cell(73, 5, substr($course->course_name, 0, 35), 1, 0, 'L');
                    $pdf->Cell(4.8, 5, $course->credits_earned, 1, 0, 'C');
                    $pdf->Cell(4.6, 5, $course->grade, 1, 0, 'C');
                    $pdf->Cell(4.9, 5, $course->result, 1, 0, 'C');
                    $row_y += 5;
                }

                // Add SGPA, CGPA, and Result in a Single Cell
                $pdf->SetXY($current_x, $row_y);
                $pdf->SetFont('Arial', 'B', 8);
                $sgpa = number_format($student->{'sgpa_' . $semester} ?? 0, 2);
                $cgpa = number_format($student->cgpa ?? 0, 2);
                $result = 'PASS'; // Placeholder, can be dynamic later
                $pdf->Cell($table_width, 5, "SGPA: $sgpa" . str_repeat(" ", 20) . "CGPA: $cgpa" . str_repeat(" ", 15) . "Result: $result", 1, 0, 'L');

                if ($semester % 2 == 0) {
                    $y_position = max($row_y + 10, $y_position + 35);
                }
            }

            // $pdf->Output();
            $pdf->Output('D', $student->student_name . ' Transcript' . '.pdf');
        } else {
            redirect('admin/timeout');
        }
    }



    public function update_certificate_log()
    {
        $usn = $this->input->post('usn');
        $details = $this->input->post('details');

        if (empty($usn) || empty($details)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
            return;
        }

        $this->load->model('Admin_model'); // Load the model
        $inserted = $this->Admin_model->insertCertificateLog($usn, $details);

        if ($inserted) {
            echo json_encode(['status' => 'success', 'message' => 'Log updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update log']);
        }
    }

    public function fetch_certificate_logs($usn)
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Student Details";
            $data['menu'] = "students";
            $this->load->model('Admin_model');

            $logs = $this->Admin_model->get_certificate_logs($usn);

            echo json_encode($logs);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }


    public function delete_marks()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Student Details";
            $data['menu'] = "students";

            log_message('debug', 'USN: ' . $usn . ' Course Code: ' . $course_code);  // Debug log

            // Ensure both parameters are received
            if (!empty($usn) && !empty($course_code)) {
                $this->db->where('usn', $usn);
                $this->db->where('subcode', $course_code);
                $this->db->delete('students_marks');

                if ($this->db->affected_rows() > 0) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error']);
                }
            } else {
                echo json_encode(['status' => 'error']);
            }
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
    public function backlogs()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Student Backlogs";
            $data['menu'] = "backlogs";

            $data['admission_years'] = $this->admin_model->get_unique_admission_years();

            $this->admin_template->show('admin/backlogs_view', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    public function fetch_backlogs()
    {
        $admission_year = $this->input->post('admission_year');
        $students = $this->admin_model->get_failed_students($admission_year);
        echo json_encode($students);
    }


    


    // $this->admin_template->show('admin/studentdetails', $data);
}
