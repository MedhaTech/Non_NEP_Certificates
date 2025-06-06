<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->CI = &get_instance();
        $this->load->model('admin_model');
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
                        $this->session->set_flashdata('message', 'Old password was Incorrect');
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
            // Retrieve session data
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Courses";
            $data['menu'] = "courses";

            // Adding "All" options for filters
            $data['programme_options'] = array("All" => "All Programmes") + $this->globals->programme();
            $data['semester_options'] = array("All" => "All Semesters") + $this->globals->semester();
            $data['branch_options'] = array("All" => "All Branches") + $this->globals->branch();

            // Set validation rules
            $this->form_validation->set_rules('programme', 'Programme', 'required');

            // Check if form data exists (i.e., user has submitted filters)
            if ($this->input->server('REQUEST_METHOD') === 'POST') {
                // Get selected filter values
                $programme = $this->input->post('programme');
                $semester = $this->input->post('semester');
                $branch = $this->input->post('branch');

                // Convert "All" options to NULL (so they don't filter anything)
                $programme = ($programme === "All") ? null : $programme;
                $semester = ($semester === "All") ? null : $semester;
                $branch = ($branch === "All") ? null : $branch;

                // Fetch filtered courses based on selected values
                $data['courses'] = $this->admin_model->getFilteredCourses($programme, $semester, $branch)->result();

                // Store selected filters to maintain state in UI
                $data['selected_programme'] = $programme;
                $data['selected_semester'] = $semester;
                $data['selected_branch'] = $branch;
            } else {
                // No data should be fetched initially
                $data['courses'] = [];
                $data['selected_programme'] = null;
                $data['selected_semester'] = null;
                $data['selected_branch'] = null;
            }
            if (empty($selected_programme) && empty($selected_branch) && empty($selected_semester)) {
                // It's the first load
                $this->session->set_flashdata('first_load', true);
            }

            // Render the view
            $this->admin_template->show('admin/courses', $data);
        } else {
            redirect('admin', 'refresh');
        }
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
            $data['programme_options'] = array(" " => "Select Programme") + $this->globals->programme();
            $data['branch_options'] = array(" " => "Select Branch") + $this->globals->branch();
            $data['semester_options'] = array(" " => "Select Semester") + $this->globals->semester();
            $data['year_options'] = array(" " => "Select Year") + $this->globals->year();

            // Set validation rules
            $this->form_validation->set_rules('course_code', 'Course Code', 'required|regex_match[/^[a-zA-Z0-9-_]*$/]');
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
            $this->form_validation->set_rules('course_code', 'Course Code', 'required|regex_match[/^[a-zA-Z0-9]*$/]');
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
            $data['admission_options'] = array("" => "Select Admission Year") + $this->globals->admissionyear();
            $data['programme_options'] = array("0" => "All Programme") + $this->globals->programme();
            $data['branch_options'] = array("0" => "All Branches") + $this->globals->branch();

            $this->form_validation->set_rules('admission_year', 'Admission Year', 'required');
            if ($this->form_validation->run() === FALSE) {
                // If validation fails, reload the page with current data
                $this->admin_template->show('admin/students', $data);
            } else {

                // Get selected filters from the POST data
                $admission_year = $this->input->post('admission_year');
                $programme = $this->input->post('programme');
                $branch = $this->input->post('branch');



                // Initialize filter conditions
                $filter_conditions = [];

                // Check if all filters are set to "All"
                $isAllSelected = $admission_year === 'All' && $programme === 'All' && $branch === 'All';

                // If all filters are set to "All", fetch all students
                if ($isAllSelected) {
                    $data['students'] = $this->admin_model->getAllStudents()->result();
                } else {
                    // If specific filters are selected, apply them
                    if ($admission_year && $admission_year !== '0' && $admission_year !== 'All') {
                        $filter_conditions['admission_year'] = $admission_year;
                    }

                    if ($programme && $programme !== '0' && $programme !== 'All') {
                        $filter_conditions['programme'] = $programme;
                    }

                    if ($branch && $branch !== '0' && $branch !== 'All') {
                        $filter_conditions['branch'] = $branch;
                    }

                    // If no filter is applied, set 'students' to an empty array (to show no data)
                    if (!empty($filter_conditions)) {
                        // Fetch filtered student list
                        $data['students'] = $this->admin_model->getFilteredStudents($filter_conditions)->result();
                    } else {
                        // If filters are selected but not all "All", show no data initially
                        $data['students'] = [];
                    }
                }

                // Pass selected filter values to the view
                $data['selected_admission_year'] = $admission_year;
                $data['selected_programme'] = $programme;
                $data['selected_branch'] = $branch;

                // Render the view
                $this->admin_template->show('admin/students', $data);
            }
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
            $this->form_validation->set_rules('usn', 'USN', 'required', [
                'required' => 'Please Enter USN'
            ]);
            // $this->form_validation->set_rules('student_name', 'Student Name', 'required|min_length[4]|alpha', [
            //     'required' => ' Please Enter a Minimum 2 Characters'
            // ]);
            $this->form_validation->set_rules(
                'student_name',
                'Student Name',
                'required|min_length[4]|regex_match[/^[a-zA-Z\s]+$/]',  // Allow alphabets and spaces
                [
                    'required' => 'Please Enter the Student Name',
                    'min_length' => 'The Student Name must be at least 4 characters in length',
                    'regex_match' => 'The Student Name can only contain alphabetic characters and spaces'
                ]
            );
            $this->form_validation->set_rules('admission_year', 'Admission Year', 'required', [
                'required' => 'Please Select Admission Year'
            ]);
            $this->form_validation->set_rules('programme', 'Programme', 'required', [
                'required' => 'Please Select Programme'
            ]);
            $this->form_validation->set_rules('branch', 'Branch', 'required', [
                'required' => 'Please Select Branch'
            ]);
            $this->form_validation->set_rules('date_of_birth', 'Date of Birth', 'required', [
                'required' => 'Please Enter/Select  Date of Birth'
            ]);
            $this->form_validation->set_rules('gender', 'Gender', 'required', [
                'required' => 'Please  Select Gender'
            ]);
            $this->form_validation->set_rules('category', 'Category', 'required', [
                'required' => 'Please Enter Category Name'
            ]);
            // $this->form_validation->set_rules('mobile', 'Mobile', 'required|regex_match[/^[0-9]{10}$/]', [
            //     'required' => 'Please Enter 10 digit Mobile Number'
            // ]);
            $this->form_validation->set_rules(
                'mobile',
                'Mobile',
                'required|regex_match[/^[0-9]{10}$/]',
                [
                    'required' => 'Please Enter 10 digit Mobile Number',
                    'regex_match' => 'The Mobile Number is not in the correct format'
                ]
            );
            $this->form_validation->set_rules(
                'parent_mobile',
                'Mobile',
                'required|regex_match[/^[0-9]{10}$/]',
                [
                    'required' => 'Please Enter 10 digit Mobile Number',
                    'regex_match' => 'The Mobile Number is not in the correct format'
                ]
            );
            $this->form_validation->set_rules(
                'father_name',
                'Father Name',
                'required|min_length[4]|regex_match[/^[a-zA-Z\s]+$/]',  // Allow alphabets and spaces
                [
                    'required' => 'Please Enter the Father Name',
                    'min_length' => 'The Father Name must be at least 4 characters in length',
                    'regex_match' => 'The Father Name can only contain alphabetic characters and spaces'
                ]
            );
            $this->form_validation->set_rules(
                'mother_name',
                'Mother Name',
                'required|min_length[4]|regex_match[/^[a-zA-Z\s]+$/]',  // Allow alphabets and spaces
                [
                    'required' => 'Please Enter the Mother Name',
                    'min_length' => 'The Mother Name must be at least 4 characters in length',
                    'regex_match' => 'The Mother Name can only contain alphabetic characters and spaces'
                ]
            );

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
                    'gender' => $this->input->post('gender'),
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
            // Set validation rules
            $this->form_validation->set_rules('usn', 'USN', 'required', [
                'required' => 'Please Enter USN'
            ]);
            // $this->form_validation->set_rules('student_name', 'Student Name', 'required|min_length[4]|alpha', [
            //     'required' => ' Please Enter a Minimum 2 Characters'
            // ]);
            $this->form_validation->set_rules(
                'student_name',
                'Student Name',
                'required|min_length[4]|regex_match[/^[a-zA-Z\s]+$/]',  // Allow alphabets and spaces
                [
                    'required' => 'Please Enter the Student Name',
                    'min_length' => 'The Student Name must be at least 4 characters in length',
                    'regex_match' => 'The Student Name can only contain alphabetic characters and spaces'
                ]
            );
            $this->form_validation->set_rules('admission_year', 'Admission Year', 'required', [
                'required' => 'Please Select Admission Year'
            ]);
            $this->form_validation->set_rules('programme', 'Programme', 'required', [
                'required' => 'Please Select Programme'
            ]);
            $this->form_validation->set_rules('branch', 'Branch', 'required', [
                'required' => 'Please Select Branch'
            ]);
            $this->form_validation->set_rules('date_of_birth', 'Date of Birth', 'required', [
                'required' => 'Please Enter/Select  Date of Birth'
            ]);
            $this->form_validation->set_rules('gender', 'Gender', 'required', [
                'required' => 'Please  Select Gender'
            ]);
            $this->form_validation->set_rules('category', 'Category', 'required', [
                'required' => 'Please Enter Category Name'
            ]);
            // $this->form_validation->set_rules('mobile', 'Mobile', 'required|regex_match[/^[0-9]{10}$/]', [
            //     'required' => 'Please Enter 10 digit Mobile Number'
            // ]);
            $this->form_validation->set_rules(
                'mobile',
                'Mobile',
                'required|regex_match[/^[0-9]{10}$/]',
                [
                    'required' => 'Please Enter 10 digit Mobile Number',
                    'regex_match' => 'The Mobile Number is not in the correct format'
                ]
            );
            $this->form_validation->set_rules(
                'parent_mobile',
                'Mobile',
                'required|regex_match[/^[0-9]{10}$/]',
                [
                    'required' => 'Please Enter 10 digit Mobile Number',
                    'regex_match' => 'The Mobile Number is not in the correct format'
                ]
            );
            $this->form_validation->set_rules(
                'father_name',
                'Father Name',
                'required|min_length[4]|regex_match[/^[a-zA-Z\s]+$/]',  // Allow alphabets and spaces
                [
                    'required' => 'Please Enter the Father Name',
                    'min_length' => 'The Father Name must be at least 4 characters in length',
                    'regex_match' => 'The Father Name can only contain alphabetic characters and spaces'
                ]
            );
            $this->form_validation->set_rules(
                'mother_name',
                'Mother Name',
                'required|min_length[4]|regex_match[/^[a-zA-Z\s]+$/]',  // Allow alphabets and spaces
                [
                    'required' => 'Please Enter the Mother Name',
                    'min_length' => 'The Mother Name must be at least 4 characters in length',
                    'regex_match' => 'The Mother Name can only contain alphabetic characters and spaces'
                ]
            );

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
                    'gender' => $this->input->post('gender'),
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
            $data['students'] = $this->admin_model->getDetails('students', $decoded_id)->row();

            // Using row() for a single course

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

            // Set validation for 'usn'
            $this->form_validation->set_rules('usn', 'USN', 'required');

            if ($this->form_validation->run() === FALSE) {
                // Validation failed, reload the page with the form
                $data['action'] = 'admin/view_studentdetails';
                $this->admin_template->show('admin/view_studentdetails', $data);
            } else {
                // Form is valid, now check if the USN exists in the database
                $usn = $this->input->post('usn');
                $details = $this->admin_model->getDetailsbyfield($usn, 'usn', 'students')->row();

                if ($details) {
                    // If the USN exists, redirect to the student details page
                    $id = $details->id;
                    $encryptId = base64_encode($id);
                    redirect('admin/studentdetails/' . $encryptId, 'refresh');
                } else {
                    // If the USN doesn't exist, set a flash message and reload the page with the error
                    $this->session->set_flashdata('message', 'Invalid USN. Please try again.');
                    $this->session->set_flashdata('status', 'alert-danger');
                    redirect('admin/view_studentdetails', 'refresh');
                }
            }
        } else {
            // If the user is not logged in, redirect to the timeout page
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

            // Fetch student marks using the USN
            $allMarks = $this->admin_model->getDetailsbysinglefield('students_marks', $data['usn'])->result(); // Fetch all records

            // Initialize an array to hold marks grouped by semester
            $data['studentmarks'] = [];

            // Loop through all marks and group them by semester
            foreach ($allMarks as $mark) {
                $semester = $mark->semester; // Assuming each mark has a semester field
                if (!isset($data['studentmarks'][$semester])) {
                    $data['studentmarks'][$semester] = []; // Initialize the array for the semester if it doesn't exist
                }

                // Fetch course name using the course code
                $course_name = $this->admin_model->getCourseNameByCode($mark->course_code); // Assuming this method exists
                $mark->course_name = $course_name; // Add course name to the mark object

                $data['studentmarks'][$semester][] = $mark; // Add the mark to the corresponding semester
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
        // Use $decoded_id instead of $id for further processing
        $decoded_id = base64_decode($id);
        $decoded_sem = base64_decode($semester);

        // Disable error reporting in production
        error_reporting(0);
        ini_set('display_errors', 0);

        // Clear all output buffers to avoid "headers already sent" issues
        while (ob_get_level()) {
            ob_end_clean();
        }

        if ($this->session->userdata('logged_in')) {
            $student = $this->admin_model->getDetails('students', $decoded_id)->row();

            if (!$student) {
                show_404();
                exit;
            }

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
            $pdf->Cell(50, 10, $student->mother_name ?? '-', 0, 1);

            $pdf->SetXY(60, 66.5);
            $pdf->Cell(50, 10, $student->father_name ?? '-', 0, 1);

            $pdf->SetXY(201, 61.2);
            $pdf->Cell(50, 10, $decoded_sem, 0, 1);

            // Fetch student marks using the USN
            $usn = $student->usn;
            $allMarks = $this->admin_model->getDetailsbysinglefield('students_marks', $usn)->result();

            // Initialize an array to hold marks grouped by semester
            $studentmarks = [];

            // Loop through all marks and group them by semester
            foreach ($allMarks as $mark) {
                $sem = $mark->semester; // Assuming each mark has a semester field
                if (!isset($studentmarks[$sem])) {
                    $studentmarks[$sem] = []; // Initialize the array for the semester if it doesn't exist
                }

                // Fetch course name using the course code
                $course_name = $this->admin_model->getCourseNameByCode($mark->course_code); // Assuming this method exists
                $mark->course_name = $course_name; // Add course name to the mark object

                $studentmarks[$sem][] = $mark; // Add the mark to the corresponding semester
            }

            // Get semester data for the specified semester
            $semester_data = $studentmarks[$decoded_sem] ?? []; // Use the semester passed to the function

            // Define fixed parameters
            $table_start_y = 85;
            $max_table_height = 64;
            $header_height = 8;

            // Calculate row height based on number of rows
            $num_rows = count($semester_data);
            $available_height = $max_table_height - $header_height;
            $row_height = ($available_height - 5) / max($num_rows, 1);

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
                $barcode_number = null;

                foreach ($semester_data as $course) {
                    $pdf->SetXY(30, $y);
                    $pdf->Cell(15, $row_height, $sno++, 1, 0, 'C', true);
                    $pdf->Cell(40, $row_height, $course->course_code, 1, 0, 'C', true);
                    $pdf->Cell(80, $row_height, $course->course_name, 1, 0, 'L', true); // Use course name here
                    $pdf->Cell(25, $row_height, $course->credits_earned ?? '-', 1, 0, 'C', true);
                    $pdf->Cell(40, $row_height, $course->grade ?? '-', 1, 0, 'C', true);
                    $pdf->Cell(40, $row_height, $course->grade_points ?? '-', 1, 1, 'C', true);
                    $y += $row_height;

                    // Add to totals
                    $total_credits_actual += $course->credits_actual ?? 0;
                    $total_credits_earned += $course->credits_earned ?? 0;

                    if (!empty($course->barcode)) {
                        $barcode_number = $course->barcode;
                    }
                }

                // Add SGPA row at the bottom of the table
                $pdf->SetXY(155, 176);
                $pdf->Cell(50, 10, $semester_data[0]->sgpa ?? '-', 0, 1);

                $pdf->SetXY(155, 164);
                $pdf->Cell(50, 10, $total_credits_actual, 0, 1);

                $pdf->SetXY(155, 170);
                $pdf->Cell(50, 10, $total_credits_earned, 0, 1);

                $pdf->SetAutoPageBreak(false);
                $pdf->SetMargins(0, 0, 0);

                $bottom_y = min(182, $pdf->GetPageHeight() - 15);
                $pdf->SetXY(155, $bottom_y);
                $pdf->Cell(50, 10, $total_credits_earned);
                // CGPA and Date
                $pdf->SetXY(155, min(188, $pdf->GetPageHeight() - 15));
                $pdf->Cell(50, 10, $semester_data[0]->cgpa ?? '-', 0, 1);

                $pdf->SetXY(78, min(188, $pdf->GetPageHeight() - 15));
                $pdf->Cell(50, 10, date('d-M-Y'));

                // Handle result year safely
                $result_year = $course->exam_period ?? 'N/A';
                $pdf->SetFont('Times', 'B', 10);
                $pdf->SetXY(201, 67.2);
                $pdf->Cell(50, 10, $result_year, 0, 1);

                // Generate Barcode
                if (!empty($barcode_number)) {
                    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                    $barcode = $generator->getBarcode($barcode_number, $generator::TYPE_CODE_128);

                    // Set local file path (for saving and reading)
                    $barcodeFileName = 'barcode_' . $barcode_number . '.png';
                    $barcodeFilePath = FCPATH . 'temp/' . $barcodeFileName; // FCPATH points to the root of your project
                    $barcodeURL = base_url('temp/' . $barcodeFileName); // only if you need the URL somewhere else

                    // Save barcode image
                    file_put_contents($barcodeFilePath, $barcode);

                    // Add barcode image to PDF using the file system path
                    $pdf->Image($barcodeFilePath, 19, 174, 50, 10);

                    // Delete temp barcode file
                    unlink($barcodeFilePath);
                }
            }

            // Clear output buffer again before sending PDF
            while (ob_get_level()) {
                ob_end_clean();
            }

            // $pdf->Output('D', $decoded_semester . ' Sem Grade Card' . '.pdf');
            // $pdf->Output();
            $pdf->Output('D', $student->usn . '_' . ' Sem_' . $decoded_sem . '_Grade Card' . '.pdf');
        } else {
            redirect('admin/timeout');
        }
    }
    public function generate_transcript_pdf($id)
    {
        $id = base64_decode($id);

        if ($this->session->userdata('logged_in')) {
            $student = $this->admin_model->getDetails('students', $id)->row();

            if (!$student) {
                show_404();
            }

            if (ob_get_contents()) ob_end_clean();

            require_once APPPATH . 'libraries/ReportPDF.php';
            require_once APPPATH . '../vendor/autoload.php';

            $programme_levels = $this->globals->programme_levels();
            $programme_key = strtolower(trim($student->programme)); // ensure lowercase match
            $programme_full_form = isset($programme_levels[$programme_key]) ? $programme_levels[$programme_key] : $student->programme;
            $branch = $this->admin_model->get_department_name_by_short($student->branch);
            $pdf = new ReportPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->Image(base_url('assets/images/transcript.png'), 0, 0, 210, 297);

            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetTextColor(174, 111, 150);

            // Student Info
            $pdf->SetXY(60, 31.5);
            $pdf->Cell(50, 6, $student->student_name);
            $pdf->SetXY(60, 37);
            $pdf->Cell(50, 6, $student->usn);
            $pdf->SetXY(60, 42);
            $pdf->Cell(50, 6, $student->admission_year);
            $pdf->SetXY(60, 47);
            $pdf->Cell(50, 6, $student->completion_year);
            $pdf->SetXY(60, 52);
            $pdf->Cell(50, 6, $programme_full_form);
            $pdf->SetTextColor(3, 76, 112);
            $pdf->SetXY(33, 58.3);
            $pdf->Cell(50, 6, $branch);
            $pdf->SetTextColor(0, 0, 0);
            // Fetch and group marks
            $usn = $student->usn;
            $allMarks = $this->admin_model->getStudentMarksOrderedByTorder($usn)->result();

            $studentmarks = [];
            foreach ($allMarks as $mark) {
                $sem = $mark->semester;
                if (!isset($studentmarks[$sem])) {
                    $studentmarks[$sem] = [];
                }
                $mark->course_name = $this->admin_model->getCourseNameByCode($mark->course_code);
                $studentmarks[$sem][] = $mark;
            }

            ksort($studentmarks);
            $unique_sems = array_keys($studentmarks);
            $sem_count = count($unique_sems);
            $completion_sem = intval($sem_count / 2);
            $pdf->SetXY(152.5, 54);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(50, 5, $completion_sem . ' Years');

            $ordered_sems = [[1, 5], [2, 6], [3, 7], [4, 8]];
            $max_y = 280;
            $left_x = 10;
            $right_x = 104; // Updated as requested
            $table_width = 89.5; // Updated as requested
            $start_y = 88;
            $page_no = 1;

            foreach ($ordered_sems as $pair) {
                $left_data = $studentmarks[$pair[0]] ?? [];
                $right_data = $studentmarks[$pair[1]] ?? [];

                $left_height = count(array_unique(array_column($left_data, 'course_code'))) * 4.5 + 9;
                $right_height = count(array_unique(array_column($right_data, 'course_code'))) * 4.5 + 9;
                $required_height = max($left_height, $right_height);

                if ($start_y + $required_height > $max_y) {
                    $pdf->AddPage();
                    $page_no++;

                    if ($page_no == 1) {
                        $pdf->Image(base_url('assets/images/transcript.png'), 0, 0, 210, 297);
                    }

                    $start_y = 25;
                    $pdf->SetFont('Arial', '', 6); // Reset font
                }

                foreach (['left' => $pair[0], 'right' => $pair[1]] as $side => $sem) {
                    if (!isset($studentmarks[$sem])) continue;

                    $x = $side === 'left' ? $left_x : $right_x;
                    $y = $start_y;

                    $sem_data = $studentmarks[$sem];
                    $exam_period = $sem_data[0]->exam_period ?? 'N/A';

                    // Header row with Semester and Session
                    $pdf->SetFont('Arial', 'B', 7);
                    $pdf->SetXY($x, $y);
                    $pdf->Cell($table_width, 4.5, '', 1); // Bordered row

                    $pdf->SetXY($x + 1.5, $y);
                    $pdf->Cell(0, 4.5, "Semester: $sem", 0, 0, 'L');

                    $right_text = "Session: $exam_period";
                    $pdf->SetXY($x + $table_width - 1.5 - $pdf->GetStringWidth($right_text), $y);
                    $pdf->SetTextColor(174, 111, 150);
                    $pdf->Cell(0, 4.5, $right_text, 0, 0, 'L');
                    $pdf->SetTextColor(0, 0, 0);
                    $row_y = $y + 4.5;
                    $pdf->SetFont('Arial', '', 6);

                    // Group courses by code
                    $grouped_courses = [];
                    foreach ($sem_data as $course) {
                        $code = $course->course_code;
                        if (!isset($grouped_courses[$code])) {
                            $grouped_courses[$code] = [];
                        }
                        $grouped_courses[$code][] = $course;
                    }

                    $count = 1;
                    foreach ($grouped_courses as $code => $attempts) {
                        $course_name = $attempts[0]->course_name;
                        $credits = $attempts[0]->credits_earned;

                        $fail_count = 1;
                        foreach ($attempts as $a) {
                            if (strtoupper($a->grade) === 'F') $fail_count++;
                        }

                        $final_result = ($fail_count === 1) ? 'P' :  'P#' . $fail_count;
                        $last_grade = strtoupper(end($attempts)->grade);
                        if ($fail_count > 1) {
                            $credits = strtoupper(end($attempts)->credits_earned);
                        }

                        $pdf->SetXY($x, $row_y);
                        $pdf->Cell(2.5, 4.5, $count++, 1, 0, 'C');
                        $pdf->Cell(70, 4.5, $course_name, 1, 0, 'L');
                        $pdf->Cell(5, 4.5, $credits, 1, 0, 'C');
                        $pdf->Cell(5, 4.5, $last_grade, 1, 0, 'C');
                        $pdf->Cell(7, 4.5, $final_result, 1, 0, 'C'); // Adjusted to fit width
                        $row_y += 4.5;
                    }

                    // Footer: SGPA, CGPA, Result
                    $pdf->SetFont('Arial', 'B', 6.5);
                    $sgpa = number_format($sem_data[0]->sgpa ?? 0, 2);
                    $cgpa = number_format($sem_data[0]->cgpa ?? 0, 2);
                    $result = 'PASS';

                    $pdf->SetXY($x, $row_y);
                    $pdf->Cell($table_width, 4.5, "SGPA: $sgpa     CGPA: $cgpa                            $result", 1);
                    $row_y += 4.5;

                    if ($side === 'left') $left_end_y = $row_y;
                    else $right_end_y = $row_y;
                }

                $start_y = max($left_end_y ?? $start_y, $right_end_y ?? $start_y);
            }

            $pdf->Output('D', $student->usn . ' Transcript' . '.pdf');
        } else {
            redirect('admin/timeout');
        }
    }

    public function generate_transcript_pdf_preview1($id)
    {
        if ($this->session->userdata('logged_in')) {
            $student = $this->admin_model->getDetails('students', $id)->row();
            if (!$student) show_404();

            if (ob_get_contents()) ob_end_clean();
            require_once APPPATH . 'libraries/ReportPDF.php';
            $programme_levels = $this->globals->programme_levels();
            $programme_key = strtolower(trim($student->programme)); // ensure lowercase match
            $programme_full_form = isset($programme_levels[$programme_key]) ? $programme_levels[$programme_key] : $student->programme;
            $branch = $this->admin_model->get_department_name_by_short($student->branch);
            $pdf = new ReportPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->Image(base_url('assets/images/transcript.png'), 0, 0, 210, 297);

            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetTextColor(174, 111, 150);

            // Student Info
            $pdf->SetXY(60, 31.5);
            $pdf->Cell(50, 6, $student->student_name);
            $pdf->SetXY(60, 37);
            $pdf->Cell(50, 6, $student->usn);
            $pdf->SetXY(60, 42);
            $pdf->Cell(50, 6, $student->admission_year);
            $pdf->SetXY(60, 47);
            $pdf->Cell(50, 6, $student->completion_year);
            $pdf->SetXY(60, 52);
            $pdf->Cell(50, 6, $programme_full_form);
            $pdf->SetTextColor(3, 76, 112);
            $pdf->SetXY(33, 58.3);
            $pdf->Cell(50, 6, $branch);
            $pdf->SetTextColor(0, 0, 0);
            // Fetch and group marks
            $usn = $student->usn;
            $allMarks = $this->admin_model->getStudentMarksOrderedByTorder($usn)->result();

            $studentmarks = [];
            foreach ($allMarks as $mark) {
                $sem = $mark->semester;
                if (!isset($studentmarks[$sem])) {
                    $studentmarks[$sem] = [];
                }
                $mark->course_name = $this->admin_model->getCourseNameByCode($mark->course_code);
                $studentmarks[$sem][] = $mark;
            }

            ksort($studentmarks);
            $unique_sems = array_keys($studentmarks);
            $sem_count = count($unique_sems);
            $completion_sem = intval($sem_count / 2);
            $pdf->SetXY(152.5, 54);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(50, 5, $completion_sem . ' Years');

            $ordered_sems = [[1, 5], [2, 6], [3, 7], [4, 8]];
            $max_y = 280;
            $left_x = 10;
            $right_x = 104; // Updated as requested
            $table_width = 89.5; // Updated as requested
            $start_y = 88;
            $page_no = 1;

            foreach ($ordered_sems as $pair) {
                $left_data = $studentmarks[$pair[0]] ?? [];
                $right_data = $studentmarks[$pair[1]] ?? [];

                $left_height = count(array_unique(array_column($left_data, 'course_code'))) * 4.5 + 9;
                $right_height = count(array_unique(array_column($right_data, 'course_code'))) * 4.5 + 9;
                $required_height = max($left_height, $right_height);

                if ($start_y + $required_height > $max_y) {
                    $pdf->AddPage();
                    $page_no++;

                    if ($page_no == 1) {
                        $pdf->Image(base_url('assets/images/transcript.png'), 0, 0, 210, 287);
                    }

                    $start_y = 25;
                    $pdf->SetFont('Arial', '', 6); // Reset font
                }

                foreach (['left' => $pair[0], 'right' => $pair[1]] as $side => $sem) {
                    if (!isset($studentmarks[$sem])) continue;

                    $x = $side === 'left' ? $left_x : $right_x;
                    $y = $start_y;

                    $sem_data = $studentmarks[$sem];
                    $exam_period = $sem_data[0]->exam_period ?? 'N/A';

                    // Header row with Semester and Session
                    $pdf->SetFont('Arial', 'B', 7);
                    $pdf->SetXY($x, $y);
                    $pdf->Cell($table_width, 4.5, '', 1); // Bordered row

                    $pdf->SetXY($x + 1.5, $y);
                    $pdf->Cell(0, 4.5, "Semester: $sem", 0, 0, 'L');

                    $right_text = "Session: $exam_period";
                    $pdf->SetXY($x + $table_width - 1.5 - $pdf->GetStringWidth($right_text), $y);
                    $pdf->SetTextColor(174, 111, 150);
                    $pdf->Cell(0, 4.5, $right_text, 0, 0, 'L');
                    $pdf->SetTextColor(0, 0, 0);
                    $row_y = $y + 4.5;
                    $pdf->SetFont('Arial', '', 6);

                    // Group courses by code
                    $grouped_courses = [];
                    foreach ($sem_data as $course) {
                        $code = $course->course_code;
                        if (!isset($grouped_courses[$code])) {
                            $grouped_courses[$code] = [];
                        }
                        $grouped_courses[$code][] = $course;
                    }

                    $count = 1;
                    foreach ($grouped_courses as $code => $attempts) {
                        $course_name = $attempts[0]->course_name;
                        $credits = $attempts[0]->credits_earned;

                        $fail_count = 1;
                        foreach ($attempts as $a) {
                            if (strtoupper($a->grade) === 'F') $fail_count++;
                        }

                        $final_result = ($fail_count === 1) ? 'P' :  'P#' . $fail_count;
                        $last_grade = strtoupper(end($attempts)->grade);
                        if ($fail_count > 1) {
                            $credits = strtoupper(end($attempts)->credits_earned);
                        }

                        $pdf->SetXY($x, $row_y);
                        $pdf->Cell(2.5, 4.5, $count++, 1, 0, 'C');
                        $pdf->Cell(70, 4.5, $course_name, 1, 0, 'L');
                        $pdf->Cell(5, 4.5, $credits, 1, 0, 'C');
                        $pdf->Cell(5, 4.5, $last_grade, 1, 0, 'C');
                        $pdf->Cell(7, 4.5, $final_result, 1, 0, 'C'); // Adjusted to fit width
                        $row_y += 4.5;
                    }

                    // Footer: SGPA, CGPA, Result
                    $pdf->SetFont('Arial', 'B', 6.5);
                    $sgpa = number_format($sem_data[0]->sgpa ?? 0, 2);
                    $cgpa = number_format($sem_data[0]->cgpa ?? 0, 2);
                    $result = 'PASS';

                    $pdf->SetXY($x, $row_y);
                    $pdf->Cell($table_width, 4.5, "SGPA: $sgpa     CGPA: $cgpa                            $result", 1);
                    $row_y += 4.5;

                    if ($side === 'left') $left_end_y = $row_y;
                    else $right_end_y = $row_y;
                }

                $start_y = max($left_end_y ?? $start_y, $right_end_y ?? $start_y);
            }
            $pdf->SetXY(10, $start_y + 1);
            $pdf->Cell(0, 3, '# Cleared in Subsequent Exams' . $start_y, 0, 1);

            $pdf->Cell(0, 3, 'P- Passed in Credit Mandatory Course', 0, 1);
            $pdf->Cell(0, 3, 'PP- Passed in Non Credit Mandatory Course', 0, 1);
            $pdf->SetXY(175, $start_y + 7); // approx. 200mm (210 - margin)
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(0, 3, 'Authentic', 0, 1, 'C');

            $pdf->SetXY(10, $start_y + 19);
            $pdf->Cell(0, 3, 'Issue Date: 07-Oct-2015     Checked By' . $pdf->GetY(), 0, 1);
            $pdf->SetXY(100, $start_y + 19); // approx. 200mm (210 - margin)
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(0, 3, 'Controller of Examinations ', 0, 0, 'L');
            $pdf->SetXY(100, $start_y + 19); // approx. 200mm (210 - margin)
            $pdf->Cell(0, 3, ' Principal', 0, 1, 'R');

            $pdf->Output($student->usn . '_transcript.pdf', 'I');
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
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'];
        $order = $this->input->post('order');
        $draw = $this->input->post('draw');

        $columns = ['students.usn', 'students.student_name', 'students.admission_year', 'students.programme', 'students.branch', 'students_marks.course_code', 'students_marks.grade'];
        $order_column_index = $order[0]['column'];
        $order_column = $columns[$order_column_index];
        $order_dir = $order[0]['dir'];

        $data = $this->admin_model->get_failed_students_paginated($admission_year, $search, $start, $length, $order_column, $order_dir);
        $total = $this->admin_model->count_all_failed_students($admission_year);
        $filtered = $this->admin_model->count_filtered_failed_students($admission_year, $search);

        echo json_encode([
            'draw' => intval($draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        ]);
    }




    // $this->admin_template->show('admin/studentdetails', $data);


    public function forgot_password_view()
    {
        $this->login_template->show('admin/forgot_password_view');
    }


    public function forgot_password()
    {
        $this->load->library('form_validation');
        $this->load->model('admin_model');
        $this->load->library('email');  // Load Email Library
        $this->config->load('email');   // Load Email Config

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', validation_errors());
            redirect('admin/');
        }

        $email = $this->input->post('email');
        $user = $this->admin_model->get_user_by_email($email);

        if (!$user) {
            $this->session->set_flashdata('message', 'Email not found!');
            redirect('admin/');
        }

        $token = bin2hex(random_bytes(20)); //token generation
        $expiry_time = date("Y-m-d H:i:s", strtotime('+5 minutes')); //set expire date and time 5 min

        $this->admin_model->store_reset_token($user->user_id, $token, $expiry_time);

        $reset_link = base_url("admin/reset_password?token=" . $token);

        // Email content
        $subject = "Password Reset Request";
        $message = "
    <div style='max-width: 500px; margin: auto; padding: 20px; font-family: Arial, sans-serif; background: #f4f4f4; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>
        <div style='background: #ffffff; padding: 20px; border-radius: 8px; text-align: center;'>
            <h2 style='color: #333;'>Password Reset Request</h2>
            <p style='color: #555;'>Hi <strong>{$user->username}</strong>,</p>
            <p style='color: #555;'>You recently requested to reset your password. Click the button below to proceed:</p>
            <a href='{$reset_link}' target='_blank' style='display: inline-block; padding: 10px 20px; margin-top: 10px; color: #fff; background: #007BFF; text-decoration: none; border-radius: 5px; font-size: 16px;'>Click Here to Reset</a>
            <p style='color: #777; font-size: 12px; margin-top: 10px;'>This link is valid for 5 minutes only.</p>
        </div>
    </div>
";



        // Email configuration

        $this->email->from('roreplayreplay@gmail.com', 'BMSCE CERTIFY NON-NEP');

        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message);

        if ($this->email->send()) {
            $this->session->set_flashdata('message', 'Password reset link sent! Check your email.');
        } else {
            $error_message = $this->email->print_debugger(['headers', 'subject', 'body']);
            $this->session->set_flashdata('message', 'Failed to send email. Debugging info: <br><pre>' . $error_message . '</pre>');
        }

        redirect('admin/');
    }

    // public function forgot_password() {
    //     $this->load->library('form_validation');
    //     $this->load->model('admin_model');
    //     $this->load->library('email');  // Load Email Library
    //     $this->config->load('email');   // Load Email Config

    //     $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

    //     if ($this->form_validation->run() == FALSE) {
    //         $this->session->set_flashdata('message', validation_errors());
    //         redirect('admin/');
    //     }

    //     $email = $this->input->post('email'); 
    //     $user = $this->admin_model->get_user_by_email($email);

    //     if (!$user) {
    //         $this->session->set_flashdata('message', 'Email not found!');
    //         redirect('admin/');
    //     }

    //     $token = bin2hex(random_bytes(20));
    //     $expiry_time = date("Y-m-d H:i:s", strtotime('+5 minutes'));

    //     $this->admin_model->store_reset_token($user->user_id, $token, $expiry_time);

    //     $reset_link = base_url("admin/reset_password?token=" . $token);

    //     // Email content
    //     $subject = "Password Reset Request";
    //     $message = "
    //         <p>Hi <strong>{$user->username}</strong>,</p>
    //         <p>You requested a password reset. Click the link below to reset your password:</p>
    //         <p><a href='{$reset_link}' target='_blank'>{$reset_link}</a></p>
    //         <p><small>This link is valid for 5 minutes only.</small></p>
    //     ";

    //     // Email configuration
    //     $this->email->from('nandeeshjkalakatti@gmail.com', 'BMSCE CERTIFY NON-NEP'); 
    //     $this->email->to($email);
    //     $this->email->subject($subject);
    //     $this->email->message($message);

    //     if ($this->email->send()) {
    //         $this->session->set_flashdata('message', 'Password reset link sent! Check your email.');
    //     } else {
    //         $this->session->set_flashdata('message', 'Failed to send email. Check email configuration.');
    //     }

    //     redirect('admin/');
    // }

    public function reset_password()
    {
        $token = $this->input->get('token'); // Token from the URL

        $this->load->model('admin_model');
        $user = $this->admin_model->verify_reset_token($token);

        if (!$user) {
            $this->session->set_flashdata('message', 'Invalid or expired token!');
            redirect('admin/');
        }

        // Load reset password form
        $data['token'] = $token;
        $this->login_template->show('admin/reset_password_view', $data);
    }

    public function update_password()
    {
        $token = $this->input->post('token');
        $new_password = $this->input->post('password');

        $this->load->model('admin_model');
        $user = $this->admin_model->verify_reset_token($token);

        if (!$user) {
            $this->session->set_flashdata('message', 'Invalid or expired token!');
            redirect('admin/');
        }

        // Hash the password 
        $hashed_password = md5($new_password);

        // Update password and invalidate the reset token
        $this->admin_model->update_password($user->user_id, $hashed_password);
        $this->admin_model->invalidate_reset_token($token);

        $this->session->set_flashdata('message', 'Password reset successfully! You can now log in.');
        redirect('admin/');
    }



    public function edit_marks($courseid, $stuid)
    {
        if ($this->session->userdata('logged_in')) {
            $data = $this->input->post();

            // Validate input
            $this->form_validation->set_rules('course_code', 'Course Code', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                $updateDetails = array(
                    'cie' => $data['cie'],
                    'see' => $data['see'],
                    'cie_see' => $data['cie_see'],
                    'grade' => $data['grade'],
                    'sgpa' => $data['sgpa'],
                    'cgpa' => $data['cgpa'],
                    'semester' => $data['semester'],
                    'grade_points' => $data['grade_points'],
                    'credits_earned' => $data['credits_earned'],
                    'credits_actual' => $data['credits_actual'],
                    'ci' => $data['ci'],
                    'suborder' => $data['suborder'],
                    'reexamyear' => $data['reexamyear'],
                    'result_year' => $data['result_year'],
                    'exam_period' => $data['exam_period'],
                    'gcno' => $data['gcno'],
                    'barcode' => $data['barcode'],
                    'torder' => $data['torder'],
                    'texam_period' => $data['texam_period'],
                );

                // Call model to update marks
                $result = $this->admin_model->update_marks($courseid, $updateDetails);

                // Flash messages based on result
                if ($result === 'no_change') {
                    $this->session->set_flashdata('message', 'No changes were made.');
                    $this->session->set_flashdata('status', 'alert-warning');
                } elseif ($result === 'updated') {
                    $this->session->set_flashdata('message', 'Data updated successfully!');
                    $this->session->set_flashdata('status', 'alert-success');
                } else {
                    $this->session->set_flashdata('message', 'No changes were made.');
                    $this->session->set_flashdata('status', 'alert-danger');
                }

                $encryptId = base64_encode($stuid);
                redirect('admin/studentdetails/' . $encryptId);
            }
        } else {
            redirect('admin');
        }
    }

    // ... existing code ...

    public function deletemarks($id, $stuid)
    {
        if ($this->session->userdata('logged_in')) {
            // Call the model to delete the course
            $result = $this->admin_model->deletemarks($id, $stuid);

            if ($result) {
                $this->session->set_flashdata('message', 'Course deleted successfully!');
                $this->session->set_flashdata('status', 'alert-success');
            } else {
                $this->session->set_flashdata('message', 'Error deleting course. Please try again.');
                $this->session->set_flashdata('status', 'alert-danger');
            }
            // Redirect back to the student details page or wherever appropriate
            $encryptId = base64_encode($stuid);
            redirect('admin/studentdetails/' . $encryptId); // Adjust the redirect as necessary
        } else {
            redirect('admin');
        }
    }

    public function transcript()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Transcript";
            $data['menu'] = "transcripts";

            // Check if USN is submitted
            $usn = $this->input->post('usn');
            if ($usn) {
                // Fetch student details using USN
                $data['students'] = $this->admin_model->getDetailsbysinglefield('students', $usn)->row();
                if ($data['students']) {
                    // Fetch student marks using the USN
                    $allMarks = $this->admin_model->getDetailsbysinglefield('students_marks', $usn)->result();

                    // Initialize an array to hold marks grouped by semester
                    $data['studentmarks'] = [];

                    // Loop through all marks and group them by semester
                    foreach ($allMarks as $mark) {
                        $semester = $mark->semester;
                        if (!isset($data['studentmarks'][$semester])) {
                            $data['studentmarks'][$semester] = [];
                        }

                        // Fetch course name using the course code
                        $course_name = $this->admin_model->getCourseNameByCode($mark->course_code);
                        $mark->course_name = $course_name;

                        $data['studentmarks'][$semester][] = $mark;
                    }
                } else {
                    $this->session->set_flashdata('status', 'alert-danger');
                    $this->session->set_flashdata('message', 'No USN found.');
                    redirect('admin/transcript', 'refresh');
                }
            }

            // Load the view
            $this->admin_template->show('admin/transcript', $data);
        } else {
            redirect('admin/timeout');
        }
    }

    public function generate_pdc_pdf($id)
    {
        $id = base64_decode($id);

        if ($this->session->userdata('logged_in')) {
            $student = $this->admin_model->getDetails('students', $id)->row();

            if (!$student) {
                show_404();
            }

            // Check if serial already exists in students_marks
            $pdc_serial = $this->admin_model->getPdcSerialFromMarks($student->usn);

            if (!$pdc_serial) {
                $year = date('y'); // e.g., 25
                $programme = strtoupper(str_replace([' ', '.', '-'], '', $student->programme)); // Normalize
                $last_serial = $this->admin_model->getLastPdcSerialNumberFromMarks($programme, $year);
                $sequence_number = str_pad($last_serial + 1, 4, '0', STR_PAD_LEFT);

                $pdc_serial = "{$programme}-{$year}-{$sequence_number}";

                // Update all rows for the student's USN with this PDC serial
                $this->admin_model->setPdcSerialInMarks($student->usn, $pdc_serial);
            }

            if (ob_get_contents()) ob_end_clean();
            require_once APPPATH . 'libraries/ReportPDF.php';
            $pdf = new ReportPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->AddCharmFont();

            // Background Image
            $pdf->Image(base_url('assets/images/DEMO_PDC.png'), 0, 0, 210, 297);
            $studentImagePath = FCPATH . "assets/student_pics/{$student->admission_year}/{$student->usn}.jpg";
            $defaultImagePath = FCPATH . "assets/student_pics/default.png";

            // Try to resolve the real path of the student image first
            $studentFilePath = file_exists($studentImagePath) ? realpath($studentImagePath) : realpath($defaultImagePath);
            $pdf->Image($studentFilePath, 90, 85, 28);
            $pdf->SetFont('Charm-Bold', '', 23);
            $pdf->SetTextColor(8, 8, 255);

            // Student Info
            $pdf->SetXY(78, 131.5);
            $pdf->Cell(45, 8, $student->student_name, 0, 1, 'C');
            $pdf->SetXY(111.5, 144);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(50, 6, $student->usn);

            // CGPA (from 8th sem)
            $semester_data = $this->admin_model->getStudentMarksBySemester($student->usn, 8);
            $cgpa = !empty($semester_data) ? number_format($semester_data[0]->cgpa ?? 0, 2) : 'N/A';

            $pdf->SetXY(98, 184);
            $pdf->Cell(50, 6, "$cgpa");

            // Date
            $current_date = date('d-m-Y');
            $pdf->SetXY(94, 258.5);
            $pdf->SetTextColor(8, 8, 255);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(50, 6, $current_date);

            // PDC Serial
            $pdf->SetXY(151, 62.5); // adjust as needed
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(50, 6, "$pdc_serial", 0, 1, 'C');
            // ➕ Programme Full Form using globals
            $programme_levels = $this->globals->programme_levels();
            $programme_key = strtolower(trim($student->programme)); // ensure lowercase match
            $programme_full_form = isset($programme_levels[$programme_key]) ? $programme_levels[$programme_key] : $student->programme;
            $branch = $this->admin_model->get_department_name_by_short($student->branch);
            // Add programme full form text to PDF
            $pdf->SetFont('Charm-Bold', '', 14);
            $pdf->SetTextColor(8, 8, 255);
            $pdf->SetXY(55, 162); // Adjust position as needed
            $pdf->Cell(110, 5, $programme_full_form, 0, 1, 'C'); // Convert to uppercase
            $pdf->SetXY(55, 167); // Adjust position as needed
            $pdf->SetFont('Charm-Bold', '', 17);
            $pdf->Cell(110, 15, strtoupper($branch), 0, 1, 'C');



            // $pdf->Output();
            $pdf->Output('D', $student->usn . ' PDC' . '.pdf');
        } else {
            redirect('admin/timeout');
        }
    }

    public function generate_pdc_pdf_prev($id)
    {
        $id = base64_decode($id);

        if ($this->session->userdata('logged_in')) {
            $student = $this->admin_model->getDetails('students', $id)->row();

            if (!$student) {
                show_404();
            }

            // Check if serial already exists in students_marks
            $pdc_serial = $this->admin_model->getPdcSerialFromMarks($student->usn);

            if (!$pdc_serial) {
                $year = date('y'); // e.g., 25
                $programme = strtoupper(str_replace([' ', '.', '-'], '', $student->programme)); // Normalize
                $last_serial = $this->admin_model->getLastPdcSerialNumberFromMarks($programme, $year);
                $sequence_number = str_pad($last_serial + 1, 4, '0', STR_PAD_LEFT);

                $pdc_serial = "{$programme}-{$year}-{$sequence_number}";

                // Update all rows for the student's USN with this PDC serial
                $this->admin_model->setPdcSerialInMarks($student->usn, $pdc_serial);
            }

            if (ob_get_contents()) ob_end_clean();
            require_once APPPATH . 'libraries/ReportPDF.php';
            $pdf = new ReportPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->AddCharmFont();
            // Background Image
            $pdf->Image(base_url('assets/images/DEMO_PDC.png'), 0, 0, 210, 297);
            $studentImagePath = FCPATH . "assets/student_pics/{$student->admission_year}/{$student->usn}.jpg";
            $defaultImagePath = FCPATH . "assets/student_pics/default.png";

            // Try to resolve the real path of the student image first
            $studentFilePath = file_exists($studentImagePath) ? realpath($studentImagePath) : realpath($defaultImagePath);
            $pdf->Image($studentFilePath, 90, 85, 28);
            $pdf->SetFont('Charm-Bold', '', 23);
            $pdf->SetTextColor(8, 8, 255);

            // Student Info
            $pdf->SetXY(78, 131.5);
            $pdf->Cell(45, 8, $student->student_name, 0, 1, 'C');
            $pdf->SetXY(111.5, 144);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(50, 6, $student->usn);

            // CGPA (from 8th sem)
            $semester_data = $this->admin_model->getStudentMarksBySemester($student->usn, 8);
            $cgpa = !empty($semester_data) ? number_format($semester_data[0]->cgpa ?? 0, 2) : 'N/A';

            $pdf->SetXY(98, 184);
            $pdf->Cell(50, 6, "$cgpa");

            // Date
            $current_date = date('d-m-Y');
            $pdf->SetXY(94, 258.5);
            $pdf->SetTextColor(8, 8, 255);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(50, 6, $current_date);

            // PDC Serial
            $pdf->SetXY(151, 62.5); // adjust as needed
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(50, 6, "$pdc_serial", 0, 1, 'C');
            // ➕ Programme Full Form using globals
            $programme_levels = $this->globals->programme_levels();
            $programme_key = strtolower(trim($student->programme)); // ensure lowercase match
            $programme_full_form = isset($programme_levels[$programme_key]) ? $programme_levels[$programme_key] : $student->programme;
            $branch = $this->admin_model->get_department_name_by_short($student->branch);
            // Add programme full form text to PDF
            $pdf->SetFont('Charm-Bold', '', 14);
            $pdf->SetTextColor(8, 8, 255);
            $pdf->SetXY(55, 162); // Adjust position as needed
            $pdf->Cell(110, 5, $programme_full_form, 0, 1, 'C'); // Convert to uppercase
            $pdf->SetXY(55, 167); // Adjust position as needed
            $pdf->SetFont('Charm-Bold', '', 17);
            $pdf->Cell(110, 15, strtoupper($branch), 0, 1, 'C');



            // $pdf->Output();
            $pdf->Output();
        } else {
            redirect('admin/timeout');
        }
    }

    // $pdf->Output('D', $student->student_name . ' PDC' . '.pdf');

    public function pdc()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "View Student PDC";
            $data['menu'] = "students";


            // Set validation rules for USN
            $this->form_validation->set_rules('usn', 'USN', 'required');

            if ($this->form_validation->run() === FALSE) {
                // If validation fails, show the search form
                $data['action'] = 'admin/pdc'; // Set action for the form
                $this->admin_template->show('admin/pdc', $data);
            } else {
                // If form is valid, look for the USN and fetch details
                $usn = $this->input->post('usn');
                $details = $this->admin_model->getDetailsbyfield($usn, 'usn', 'students')->row();
                if ($details) {
                    $data['students'] = $details;
                    $data['page_title'] = "PDC for USN: $usn";
                    $this->admin_template->show('admin/pdc', $data);
                } else {
                    $this->session->set_flashdata('error', 'No data found for the selected USN and semester/attempt.');
                    redirect('admin/pdc', 'refresh');
                }
            }
        } else {
            redirect('admin/timeout');
        }
    }

    public function get_grade_card()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Generate Grade Card";
            $data['menu'] = "gradecard";

            // Show the view
            $this->admin_template->show('admin/get_grade_card', $data);
        } else {
            redirect('admin/timeout');
        }
    }

    public function fetch_semester_options()
    {
        $usn = $this->input->post('usn');

        if (empty($usn)) {
            echo json_encode(['status' => 'error', 'message' => 'Please enter a USN']);
            return;
        }

        // Get student details
        $student = $this->admin_model->getStudentByUSN($usn);

        if (!$student) {
            echo json_encode(['status' => 'error', 'message' => 'Student not found']);
            return;
        }

        // Get all marks for this student
        $this->db->select('*, YEAR(result_year) as exam_year, MONTH(result_year) as exam_month');
        $this->db->where('usn', $usn);
        $this->db->order_by('result_year', 'ASC');
        $marks = $this->db->get('students_marks')->result();

        $options = [];
        $supplementaryYears = [];

        foreach ($marks as $mark) {
            $semester = $mark->semester;
            $examYear = $mark->exam_year;
            $examMonth = $mark->exam_month;

            if ($examMonth == 7) { // Supplementary exam
                if (!isset($supplementaryYears[$examYear])) {
                    $supplementaryYears[$examYear] = [
                        'semesters' => [],
                        'result_year' => $mark->result_year
                    ];
                }
                if (!in_array($semester, $supplementaryYears[$examYear]['semesters'])) {
                    $supplementaryYears[$examYear]['semesters'][] = $semester;
                }
            } else { // Regular exam
                $option = [
                    'semester' => $semester,
                    'year' => $examYear,
                    'label' => 'R_' . $semester,
                    'type' => 'regular',
                    'result_year' => $mark->result_year
                ];

                // Only add if not already present
                if (!$this->optionExists($options, $option)) {
                    $options[] = $option;
                }
            }
        }

        // Add supplementary options
        $sequence = 1;
        foreach ($supplementaryYears as $year => $data) {
            sort($data['semesters']); // Sort semesters numerically
            $option = [
                'year' => $year,
                'label' => 'S' . $sequence,
                'sequence' => $sequence,
                'type' => 'supplementary',
                'semesters' => $data['semesters'],
                'result_year' => $data['result_year']
            ];
            $options[] = $option;
            $sequence++;
        }

        echo json_encode(['status' => 'success', 'options' => $options]);
    }

    private function optionExists($options, $newOption)
    {
        foreach ($options as $option) {
            if (
                $option['semester'] == $newOption['semester'] &&
                $option['year'] == $newOption['year'] &&
                $option['type'] == $newOption['type']
            ) {
                return true;
            }
        }
        return false;
    }

    public function generate_grade_card()
    {
        $usn = $this->input->post('usn');
        $semester_option = $this->input->post('semester_option');

        if (empty($usn) || empty($semester_option)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input parameters']);
            return;
        }

        // Parse the semester option
        $type = substr($semester_option, 0, 1); // 'R' for regular or 'S' for supplementary

        if ($type === 'R') {
            $semester = substr($semester_option, 2);
            $is_supplementary = false;
            $sequence = null;
            $marks = $this->admin_model->getStudentRegularMarks($usn, $semester);
        } else {
            $sequence = substr($semester_option, 1);
            $is_supplementary = true;
            $marks = $this->admin_model->getStudentSupplementaryMarks($usn, $sequence);

            if (!empty($marks)) {
                $semesters = array_unique(array_column($marks, 'semester'));
                sort($semesters);
                $semester = implode(', ', $semesters);
            } else {
                $semester = '';
            }
        }

        $student = $this->admin_model->getStudentByUSN($usn);

        if (!$student) {
            echo json_encode(['status' => 'error', 'message' => 'Student not found']);
            return;
        }

        // Load and render the grade card template
        $data = [
            'student' => $student,
            'marks' => $marks,
            'semester' => $semester,
            'is_supplementary' => $is_supplementary,
            'sequence' => $sequence,
            'exam_period' => $result_year
        ];

        $html = $this->load->view('admin/grade_card_template', $data, true);
        echo json_encode(['status' => 'success', 'html' => $html]);
    }

    public function print_grade_card()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('admin/timeout');
            return;
        }

        $usn = $this->input->get('usn');
        $semester = $this->input->get('semester');
        $is_supplementary = $this->input->get('is_supplementary');
        $sequence = $this->input->get('sequence');

        if (empty($usn) || empty($semester)) {
            $this->session->set_flashdata('error', 'USN and Semester are required');
            redirect('admin/get_grade_card');
            return;
        }

        // Get student ID from USN
        $student = $this->admin_model->getStudentByUSN($usn);

        if (empty($student)) {
            $this->session->set_flashdata('error', 'Student not found');
            redirect('admin/get_grade_card');
            return;
        }

        // Log the certificate download
        $semLabel = $semester;
        if ($is_supplementary == 'true') {
            $semLabel = $semester . 'S' . $sequence;
        }

        // Get result date for supplementary if needed
        if ($is_supplementary == 'true' && !empty($sequence)) {
            // Get the supplementary exam marks first to get the result year
            $suppMarks = $this->admin_model->getStudentSupplementaryMarks($usn, $semester, $sequence);

            if (!empty($suppMarks)) {
                // Add result year to session for PDF generation
                $this->session->set_userdata('pdf_result_year', $suppMarks[0]->result_year);
            }
        }

        $details = 'Grade Card (Sem: ' . $semLabel . ($is_supplementary == 'true' ? ', Supplementary)' : ', Regular)');
        $this->admin_model->insertCertificateLog($usn, $details);

        // Use the existing generate_student_pdf function
        redirect('admin/generate_student_pdf/' . $student->id . '/' . $semester);
    }
    public function generate_grade_card_pdf($id, $semester, $is_supplementary = 0, $sequence = null)
    {
        // Decode the parameters
        $decoded_id = base64_decode($id);
        $decoded_semester = base64_decode($semester);
        $is_supplementary = base64_decode($is_supplementary);
        $sequence = base64_decode($sequence);
        $is_supplementary = ($is_supplementary == 1);

        if ($this->session->userdata('logged_in')) {
            $student = $this->admin_model->getStudentByUSN($decoded_id);

            if (!$student) {
                show_404();
                exit;
            }

            require_once APPPATH . 'libraries/ReportPDF.php';
            require_once APPPATH . '../vendor/autoload.php';

            $pdf = new ReportPDF('L', 'mm', 'A4');
            $pdf->AddPage();

            // Background Image
            $pdf->Image(base_url('assets/images/certificate_bg.png'), 0, 0, 297, 210);

            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);

            $pdf->SetXY(66, 72);
            $pdf->Cell(50, 10, $student->usn, 0, 1);

            $pdf->SetXY(61, 56);
            $pdf->Cell(50, 10, $student->student_name, 0, 1);

            $pdf->SetXY(60, 61.3);
            $pdf->Cell(50, 10, $student->mother_name ?? '-', 0, 1);

            $pdf->SetXY(60, 66.5);
            $pdf->Cell(50, 10, $student->father_name ?? '-', 0, 1);

            // Modified semester display
            $pdf->SetXY(201, 61.2);
            if ($is_supplementary) {
                $semesterText = "Supplementary ";
                $pdf->Cell(50, 10, $semesterText, 0, 1);
            } else {
                // Check if semester is odd or even
                $romanSemester = ($decoded_semester % 2 == 1) ? 'I' : 'II';
                $pdf->Cell(50, 10,  $romanSemester, 0, 1);
            }


            // Fetch marks based on semester type
            if ($is_supplementary) {
                // ✅ Ensure sequence is provided
                if (!$sequence) {
                    // Try fetching the latest sequence for this semester
                    $sequence = $this->admin_model->getLatestSupplementarySequence($decoded_id, $decoded_semester);
                }

                // ✅ Retrieve supplementary marks
                $marks = $this->admin_model->getStudentSupplementaryMarks($decoded_id, $sequence);
            } else {
                // ✅ Retrieve regular semester marks
                $marks = $this->admin_model->getStudentRegularMarks($decoded_id, $decoded_semester);
            }

            if (empty($marks)) {
                echo "No marks available.";
                exit;
            }

            // Generate and output PDF
            $this->renderPDF($pdf, $student, $marks, $decoded_semester, $is_supplementary, $sequence);

            // Modified filename generation
            if ($is_supplementary) {
                $filename = $student->usn . '_Supplementary_' . $sequence . '_Grade_Card.pdf';
            } else {
                $filename = $student->usn . '_Sem_' . $decoded_semester . '_Grade_Card.pdf';
            }

            // $pdf->Output();
            $pdf->Output('D', $filename);
        } else {
            redirect('admin/timeout');
        }
    }
    public function getLatestSupplementarySequence($usn, $semester)
    {
        $this->db->select('MAX(sequence) as latest_sequence');
        $this->db->from('students_marks');
        $this->db->where('usn', $usn);
        $this->db->where('semester', $semester);
        $this->db->where('subcode LIKE', 'S%'); // Ensure it's a supplementary mark

        $query = $this->db->get();
        $result = $query->row();

        return ($result && $result->latest_sequence) ? $result->latest_sequence : 1; // Default to sequence 1 if none found
    }


    private function renderPDF($pdf, $student, $marks, $semester, $is_supplementary, $sequence)
    {
        // Set font
        // Define fixed parameters
        $table_start_y = 85;
        $max_table_height = 64;
        $header_height = 8;

        // Calculate row height based on number of rows
        $num_rows = count($marks);
        $available_height = $max_table_height - $header_height;
        $row_height = ($available_height - 5) / max($num_rows, 1);

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
        // Table data
        $sno = 1;
        $y = $table_start_y + $header_height;

        foreach ($marks as $mark) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY(30, $y);
            $pdf->Cell(15, $row_height, $sno++, 1, 0, 'C', true);
            $pdf->Cell(40, $row_height, $mark->course_code, 1, 0, 'C', true);
            $pdf->Cell(80, $row_height, $mark->course_name, 1, 0, 'L', true); // Use course name here
            $pdf->Cell(25, $row_height, $mark->credits_earned ?? '-', 1, 0, 'C', true);
            $pdf->Cell(40, $row_height, $mark->grade ?? '-', 1, 0, 'C', true);
            $pdf->Cell(40, $row_height, $mark->grade_points ?? '-', 1, 1, 'C', true);
            $y += $row_height;
            // Add to totals
            $total_credits_actual += $mark->credits_actual ?? 0;
            $total_credits_earned += $mark->credits_earned ?? 0;
            if (!empty($mark->barcode)) {
                $barcode_number = $mark->barcode;
            }
        }
        $pdf->SetXY(155, 176);
        $pdf->Cell(50, 10, $marks[0]->sgpa ?? '-', 0, 1);

        $pdf->SetXY(155, 164);
        $pdf->Cell(50, 10, $marks[0]->ci, 0, 1);

        $pdf->SetXY(155, 170);
        $pdf->Cell(50, 10, $total_credits_earned, 0, 1);

        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);

        $bottom_y = min(182, $pdf->GetPageHeight() - 15);
        $pdf->SetXY(155, $bottom_y);
        $pdf->Cell(50, 10, $total_credits_earned);
        // CGPA and Date
        $pdf->SetXY(155, min(188, $pdf->GetPageHeight() - 15));
        $pdf->Cell(50, 10, $marks[0]->cgpa ?? '-', 0, 1);

        $pdf->SetXY(78, min(188, $pdf->GetPageHeight() - 15));
        $pdf->Cell(50, 10, date('d-M-Y'));


        $pdf->SetXY(34, min(187.3, $pdf->GetPageHeight() - 13));
        $pdf->Cell(50, 10, $mark->gcno);

        // Handle result year safely
        $result_year = $mark->exam_period ?? 'N/A';
        $pdf->SetFont('Times', 'B', 10);
        $pdf->SetXY(201, 67.2);
        $pdf->Cell(50, 10, $result_year, 0, 1);
        if (!empty($barcode_number)) {
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode($barcode_number, $generator::TYPE_CODE_128);

            // Set local file path (for saving and reading)
            $barcodeFileName = 'barcode_' . $barcode_number . '.png';
            $barcodeFilePath = FCPATH . 'temp/' . $barcodeFileName; // FCPATH points to the root of your project
            $barcodeURL = base_url('temp/' . $barcodeFileName); // only if you need the URL somewhere else

            // Save barcode image
            file_put_contents($barcodeFilePath, $barcode);

            // Add barcode image to PDF using the file system path
            $pdf->Image($barcodeFilePath, 19, 174, 50, 10);

            // Delete temp barcode file
            unlink($barcodeFilePath);
        }
    }


    public function update_completion_year()
    {
        if ($this->session->userdata('logged_in')) {
            $student_id = $this->input->post('student_id');
            $completion_year = $this->input->post('completion_year');

            if (!empty($student_id) && !empty($completion_year)) {
                $this->admin_model->updateData('students', ['completion_year' => $completion_year], $student_id);
                $this->session->set_flashdata('message', 'Year of Completion updated successfully!');
                $this->session->set_flashdata('status', 'alert-success');
                redirect('admin/transcript?usn=' . urlencode($this->input->post('usn')));
            } else {
                $this->session->set_flashdata('message', 'Invalid input.');
                $this->session->set_flashdata('status', 'alert-danger');
            }

            redirect('admin/transcript', 'refresh');
        } else {
            redirect('admin/timeout');
        }
    }



    public function get_grade_card_details()
    {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Grade Card Details";
            $data['menu'] = "students";

            $data['usn'] = $this->input->post('usn');
            $data['students'] = $this->admin_model->getDetailsbysinglefield('students', $data['usn'])->row();
            // Fetch student marks using the USN
            $allMarks = $this->admin_model->getDetailsbysinglefield('students_marks', $data['usn'])->result(); // Fetch all records

            // Initialize an array to hold marks grouped by semester
            $data['studentmarks'] = [];

            // Loop through all marks and group them by semester
            $resultYears = [];

            foreach ($allMarks as $mark) {
                $result_date = $mark->result_year;

                // Initialize the group
                if (!isset($data['studentmarks'][$result_date])) {
                    $data['studentmarks'][$result_date] = [];
                }

                $mark->course_name = $this->admin_model->getCourseNameByCode($mark->course_code);
                $data['studentmarks'][$result_date][] = $mark;
            }

            // Sort result dates in ascending order
            ksort($data['studentmarks']);
            $data['ResultPeriodCount'] = count($data['studentmarks']);



            // Show the view
            $this->admin_template->show('admin/studentdetails_grade', $data);
        } else {
            redirect('admin/timeout');
        }
    }
    public function generate_grade_card_pdf_details($id, $resultDate, $is_supplementary = 0)
    {
        // Decode the parameters
        $decoded_id = base64_decode($id);
        $decoded_resultDate = base64_decode($resultDate);
        $is_supplementary = base64_decode($is_supplementary);
        $is_supplementary = ($is_supplementary == 1);

        if ($this->session->userdata('logged_in')) {
            $student = $this->admin_model->getStudentByUSN($decoded_id);

            if (!$student) {
                show_404();
                exit;
            }

            require_once APPPATH . 'libraries/ReportPDF.php';
            require_once APPPATH . '../vendor/autoload.php';

            $pdf = new ReportPDF('L', 'mm', 'A4');
            $pdf->AddPage();

            // Background Image
            $pdf->Image(base_url('assets/images/certificate_bg.png'), 0, 0, 297, 210);

            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetTextColor(171, 57, 112);

            $pdf->SetXY(66, 72);
            $pdf->Cell(50, 10, $student->usn, 0, 1);

            $pdf->SetXY(61, 56);
            $pdf->Cell(50, 10, $student->student_name, 0, 1);

            $pdf->SetXY(60, 61.3);
            $pdf->Cell(50, 10, $student->mother_name ?? '-', 0, 1);

            $pdf->SetXY(60, 66.5);
            $pdf->Cell(50, 10, $student->father_name ?? '-', 0, 1);
            $studentImagePath = FCPATH . "assets/student_pics/{$student->admission_year}/{$student->usn}.jpg";
            $defaultImagePath = FCPATH . "assets/student_pics/default.png";

            // Try to resolve the real path of the student image first
            $studentFilePath = file_exists($studentImagePath) ? realpath($studentImagePath) : realpath($defaultImagePath);
            $pdf->Image($studentFilePath, 250, 45, 26);

            // Modified semester display
            $pdf->SetXY(201, 61.2);
            if ($is_supplementary) {
                $semesterText = "Supplementary ";
                $pdf->SetTextColor(171, 57, 112);
                $pdf->Cell(50, 10, $semesterText, 0, 1);
            } else {

                $romanSemester = ($decoded_semester % 2 == 1) ? 'I' : 'II';
                $pdf->SetTextColor(171, 57, 112);
                $pdf->Cell(50, 10,  $romanSemester, 0, 1);
            }



            $marks = $this->admin_model->getStudentRegularMarksdetails($decoded_id, $decoded_resultDate);


            if (empty($marks)) {
                echo "No marks available.";
                exit;
            }

            // Generate and output PDF
            $this->renderPDFdetails($pdf, $student, $marks, $decoded_resultDate, $is_supplementary);

            // Modified filename generation
            if ($is_supplementary) {
                $filename = $student->usn . '_Supplementary_' . $decoded_resultDate . '_Grade_Card.pdf';
            } else {
                $filename = $student->usn . '_Regular_' . $decoded_resultDate . '_Grade_Card.pdf';
            }

            // $pdf->Output();
            $pdf->Output('D', $filename);
        } else {
            redirect('admin/timeout');
        }
    }
    private function renderPDFdetails($pdf, $student, $marks, $resultDate, $is_supplementary)
    {
        // Set font
        // Define fixed parameters
        $table_start_y = 85;
        $max_table_height = 64;
        $header_height = 8;

        // Calculate row height based on number of rows
        $num_rows = count($marks);
        $available_height = $max_table_height - $header_height;
        $row_height = ($available_height - 5) / max($num_rows, 1);

        // Table Headers
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(231, 119, 22);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetXY(24, $table_start_y);
        $pdf->Cell(15, $header_height, 'Sl.No.', 1, 0, 'C', true);
        $pdf->Cell(40, $header_height, 'Course Code', 1, 0, 'C', true);
        $pdf->Cell(110, $header_height, 'Course Title', 1, 0, 'C', true);
        $pdf->Cell(20, $header_height, 'Credits', 1, 0, 'C', true);
        $pdf->Cell(35, $header_height, 'Grade Awarded', 1, 0, 'C', true);
        $pdf->Cell(35, $header_height, 'Grade Points', 1, 1, 'C', true);
        // Table data
        $sno = 1;
        $y = $table_start_y + $header_height;

        foreach ($marks as $mark) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY(24, $y);
            $pdf->Cell(15, $row_height, $sno++, 1, 0, 'C', true);
            $pdf->Cell(40, $row_height, $mark->course_code, 1, 0, 'C', true);
            $pdf->Cell(110, $row_height, $mark->course_name, 1, 0, 'L', true); // Use course name here
            $pdf->Cell(20, $row_height, $mark->credits_earned ?? '-', 1, 0, 'C', true);
            $pdf->Cell(35, $row_height, $mark->grade ?? '-', 1, 0, 'C', true);
            $pdf->Cell(35, $row_height, $mark->grade_points ?? '-', 1, 1, 'C', true);
            $y += $row_height;
            // Add to totals
            $total_credits_actual += $mark->credits_actual ?? 0;
            $total_credits_earned += $mark->credits_earned ?? 0;
            if (!empty($mark->barcode)) {
                $barcode_number = $mark->barcode;
            }
        }
        $pdf->SetXY(155, 176);
        $pdf->Cell(50, 10, $marks[0]->sgpa ?? '-', 0, 1);

        $pdf->SetXY(155, 164);
        $pdf->Cell(50, 10, $marks[0]->ci, 0, 1);

        $pdf->SetXY(155, 170);
        $pdf->Cell(50, 10, $total_credits_earned, 0, 1);

        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);

        $bottom_y = min(182, $pdf->GetPageHeight() - 15);
        $pdf->SetXY(155, $bottom_y);
        $pdf->Cell(50, 10, $total_credits_earned);
        // CGPA and Date
        $pdf->SetXY(155, min(188, $pdf->GetPageHeight() - 15));
        $pdf->Cell(50, 10, $marks[0]->cgpa ?? '-', 0, 1);

        $pdf->SetXY(78, min(188, $pdf->GetPageHeight() - 15));
        $pdf->SetTextColor(42, 56, 223);
        $pdf->Cell(50, 10, date('d-M-Y'));


        $pdf->SetXY(34, min(187.3, $pdf->GetPageHeight() - 13));
        $pdf->SetTextColor(42, 56, 223);
        $pdf->Cell(50, 10, $mark->gcno);

        // Handle result year safely
        $result_year = $mark->exam_period ?? 'N/A';
        $pdf->SetFont('Times', 'B', 10);
        $pdf->SetXY(201, 67.2);
        $pdf->SetTextColor(171, 57, 112);
        $pdf->Cell(50, 10, $result_year, 0, 1);
        if (!empty($barcode_number)) {
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode($barcode_number, $generator::TYPE_CODE_128);

            // Set local file path (for saving and reading)
            $barcodeFileName = 'barcode_' . $barcode_number . '.png';
            $barcodeFilePath = FCPATH . 'temp/' . $barcodeFileName; // FCPATH points to the root of your project
            $barcodeURL = base_url('temp/' . $barcodeFileName); // only if you need the URL somewhere else

            // Save barcode image
            file_put_contents($barcodeFilePath, $barcode);

            // Add barcode image to PDF using the file system path
            $pdf->Image($barcodeFilePath, 24, 174, 50, 10);
            $signImagePath = FCPATH . "assets/student_pics/sign.png";
            $pdf->Image($signImagePath, 240, 175, 30);

            // Delete temp barcode file
            unlink($barcodeFilePath);
        }
    }

    public function generate_bulk_grade_pdf($id)
    {
        // Decode the parameters
        $decoded_id = base64_decode($id);


        if ($this->session->userdata('logged_in')) {
            $student = $this->admin_model->getStudentByUSN($decoded_id);

            if (!$student) {
                show_404();
                exit;
            }

            $allMarks = $this->admin_model->getDetailsbysinglefield('students_marks',  $decoded_id)->result(); // Fetch all records

            // Initialize an array to hold marks grouped by semester
            $studentmarks = [];

            // Loop through all marks and group them by semester
            $resultYears = [];

            foreach ($allMarks as $mark) {
                $result_date = $mark->result_year;

                // Initialize the group
                if (!isset($data['studentmarks'][$result_date])) {
                    $data['studentmarks'][$result_date] = [];
                }

                $studentmarks[$result_date][] = $mark;
            }

            // Sort result dates in ascending order
            ksort($studentmarks);

            require_once APPPATH . 'libraries/ReportPDF.php';
            require_once APPPATH . '../vendor/autoload.php';

            $pdf = new ReportPDF('L', 'mm', 'A4');
            foreach ($studentmarks as $resultDate => $subjects):

                $is_supplementary = 0;

                if (date('n', strtotime($resultDate)) == 7) {
                    $is_supplementary = 1;
                }
                $decoded_resultDate = $resultDate;


                $pdf->AddPage();

                // Background Image
                $pdf->Image(base_url('assets/images/certificate_bg.png'), 0, 0, 297, 210);

                $pdf->SetFont('Times', 'B', 10);
                $pdf->SetTextColor(171, 57, 112);

                $pdf->SetXY(66, 72);
                $pdf->Cell(50, 10, $student->usn, 0, 1);

                $pdf->SetXY(61, 56);
                $pdf->Cell(50, 10, $student->student_name, 0, 1);

                $pdf->SetXY(60, 61.3);
                $pdf->Cell(50, 10, $student->mother_name ?? '-', 0, 1);

                $pdf->SetXY(60, 66.5);
                $pdf->Cell(50, 10, $student->father_name ?? '-', 0, 1);
                $studentImagePath = FCPATH . "assets/student_pics/{$student->admission_year}/{$student->usn}.jpg";
                $defaultImagePath = FCPATH . "assets/student_pics/default.png";

                // Try to resolve the real path of the student image first
                $studentFilePath = file_exists($studentImagePath) ? realpath($studentImagePath) : realpath($defaultImagePath);
                $pdf->Image($studentFilePath, 250, 45, 26);

                // Modified semester display
                $pdf->SetXY(201, 61.2);
                if ($is_supplementary) {
                    $semesterText = "Supplementary ";
                    $pdf->SetTextColor(171, 57, 112);
                    $pdf->Cell(50, 10, $semesterText, 0, 1);
                } else {

                    $romanSemester = ($decoded_semester % 2 == 1) ? 'I' : 'II';
                    $pdf->SetTextColor(171, 57, 112);
                    $pdf->Cell(50, 10,  $romanSemester, 0, 1);
                }



                $marks = $this->admin_model->getStudentRegularMarksdetails($decoded_id, $decoded_resultDate);


                if (empty($marks)) {
                    echo "No marks available.";
                    exit;
                }

                // Generate and output PDF
                $this->renderPDFdetails($pdf, $student, $marks, $decoded_resultDate, $is_supplementary);

            // Modified filename generation

            endforeach;
            $filename = $student->usn . '_Grade_Card.pdf';
            $pdf->Output('D', $filename);
        } else {
            redirect('admin/timeout');
        }
    }

    public function branch_grade_card()
    {
        if ($this->session->userdata('logged_in')) {
            // Retrieve session data
            $session_data = $this->session->userdata('logged_in');
            $data['id'] = $session_data['id'];
            $data['username'] = $session_data['username'];
            $data['full_name'] = $session_data['full_name'];
            $data['role'] = $session_data['role'];

            $data['page_title'] = "Exam wise Generate Grade Card";
            $data['menu'] = "students";

            $data['action'] = 'admin/branch_grade_card';
            $data['programme_options'] = array("" => "Select Programme") + $this->globals->programme();
            $data['branch_options'] = array("" => "Select Branch") + $this->globals->branch();
            $data['result_year_options'] =  $this->admin_model->get_dropdown_data();
            $this->form_validation->set_rules('programme', 'Programme', 'required');
            $this->form_validation->set_rules('branch', 'Branch', 'required');
            $this->form_validation->set_rules('result_year', 'Result Year', 'required');
            if ($this->form_validation->run() === FALSE) {
                // If validation fails, reload the page with current data
                $this->admin_template->show('admin/branch_grade_card', $data);
            } else {

                $resultDate = $this->input->post('result_year');
                $programme = $this->input->post('programme');
                $branch = $this->input->post('branch');


                $students = $this->admin_model->get_students_by_branch_programme($branch, $programme, $resultDate)->result();
                // var_dump($this->db->last_query());
                // die();
                require_once APPPATH . 'libraries/ReportPDF.php';
                require_once APPPATH . '../vendor/autoload.php';
                $pdf = new ReportPDF('L', 'mm', 'A4');
                foreach ($students as $student) {
                    $marks = $this->admin_model->getStudentRegularMarksdetails($student->usn, $resultDate);


                    if (!empty($marks)) {

                        $is_supplementary = 0;

                        if (date('n', strtotime($resultDate)) == 7) {
                            $is_supplementary = 1;
                        }


                        $pdf->AddPage();
                        $student = $this->admin_model->getStudentByUSN($student->usn);
                        // Background Image
                        $pdf->Image(base_url('assets/images/certificate_bg.png'), 0, 0, 297, 210);

                        $pdf->SetFont('Times', 'B', 10);
                        $pdf->SetTextColor(171, 57, 112);

                        $pdf->SetXY(66, 72);
                        $pdf->Cell(50, 10, $student->usn, 0, 1);

                        $pdf->SetXY(61, 56);
                        $pdf->Cell(50, 10, $student->student_name, 0, 1);

                        $pdf->SetXY(60, 61.3);
                        $pdf->Cell(50, 10, $student->mother_name ?? '-', 0, 1);

                        $pdf->SetXY(60, 66.5);
                        $pdf->Cell(50, 10, $student->father_name ?? '-', 0, 1);
                        $studentImagePath = FCPATH . "assets/student_pics/{$student->admission_year}/{$student->usn}.jpg";
                        $defaultImagePath = FCPATH . "assets/student_pics/default.png";

                        // Try to resolve the real path of the student image first
                        $studentFilePath = file_exists($studentImagePath) ? realpath($studentImagePath) : realpath($defaultImagePath);
                        $pdf->Image($studentFilePath, 250, 45, 26);

                        // Modified semester display
                        $pdf->SetXY(201, 61.2);
                        if ($is_supplementary) {
                            $semesterText = "Supplementary ";
                            $pdf->SetTextColor(171, 57, 112);
                            $pdf->Cell(50, 10, $semesterText, 0, 1);
                        } else {

                            $romanSemester = ($decoded_semester % 2 == 1) ? 'I' : 'II';
                            $pdf->SetTextColor(171, 57, 112);
                            $pdf->Cell(50, 10,  $romanSemester, 0, 1);
                        }





                        // Generate and output PDF
                        $this->renderPDFdetails($pdf, $student, $marks, $resultDate, $is_supplementary);
                    }
                }
                $filename = $programme . '_' . $branch . '_' . $resultDate . '_Grade_Card.pdf';
                $pdf->Output('D', $filename);

                redirect('admin/branch_grade_card', 'refresh');
                // $this->admin_template->show('admin/branch_grade_card', $data);
            }
        } else {
            redirect('admin', 'refresh');
        }
    }

    public function generate_transcript_pdf_preview($id)
    {
        if ($this->session->userdata('logged_in')) {
            $student = $this->admin_model->getDetails('students', $id)->row();
            if (!$student) show_404();

            if (ob_get_contents()) ob_end_clean();
            require_once APPPATH . 'libraries/ReportPDF.php';
            $programme_levels = $this->globals->programme_levels();
            $programme_key = strtolower(trim($student->programme)); // ensure lowercase match
            $programme_full_form = isset($programme_levels[$programme_key]) ? $programme_levels[$programme_key] : $student->programme;
            $branch = $this->admin_model->get_department_name_by_short($student->branch);
            $pdf = new ReportPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->Image(base_url('assets/images/transcript.png'), 0, 0, 210, 297);

            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetTextColor(174, 111, 150);

            // Student Info
            $pdf->SetXY(60, 31.5);
            $pdf->Cell(50, 6, $student->student_name);
            $pdf->SetXY(60, 37);
            $pdf->Cell(50, 6, $student->usn);
            $pdf->SetXY(60, 42);
            $pdf->Cell(50, 6, $student->admission_year);
            $pdf->SetXY(60, 47);
            $pdf->Cell(50, 6, $student->completion_year);
            $pdf->SetXY(60, 52);
            $pdf->Cell(50, 6, $programme_full_form);
            $pdf->SetTextColor(3, 76, 112);
            $pdf->SetXY(33, 58.3);
            $pdf->Cell(50, 6, $branch);
            $pdf->SetTextColor(0, 0, 0);
            // Fetch and group marks
            $usn = $student->usn;
            $allMarks = $this->admin_model->getStudentMarksOrderedByTorder($usn)->result();

            $studentmarks = [];
            foreach ($allMarks as $mark) {
                $sem = $mark->semester;
                if (!isset($studentmarks[$sem])) {
                    $studentmarks[$sem] = [];
                }
                $mark->course_name = $this->admin_model->getCourseNameByCode($mark->course_code);
                $studentmarks[$sem][] = $mark;
            }

            ksort($studentmarks);
            $unique_sems = array_keys($studentmarks);
            $sem_count = count($unique_sems);
            $completion_sem = intval($sem_count / 2);
            $pdf->SetXY(152.5, 54);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(50, 5, $completion_sem . ' Years');

            $sem_order = [1, 5, 2, 6, 3, 7, 4, 8];
            $max_y = 290;
            $footer_buffer = 32; // Reserve space for final footer
            $left_x = 10;
            $right_x = 104;
            $table_width = 89.5;
            $left_y = $right_y = 88;
            $page_no = 1;
            $row_height = 4.0;
            $header_footer_height = 9;
            $col_toggle = 'left'; // Alternate columns

            foreach ($sem_order as $sem) {
                if (!isset($studentmarks[$sem])) continue;

                $x = $col_toggle === 'left' ? $left_x : $right_x;
                $y = $col_toggle === 'left' ? $left_y : $right_y;

                $sem_data = $studentmarks[$sem];
                $exam_period = $sem_data[0]->exam_period ?? 'N/A';

                // Group by course code
                $grouped_courses = [];
                foreach ($sem_data as $course) {
                    $grouped_courses[$course->course_code][] = $course;
                }

                $block_height = count($grouped_courses) * $row_height + $header_footer_height + $row_height;

                // Check if block fits, including footer buffer
                if ($y + $block_height + $footer_buffer > $max_y) {
                    $pdf->AddPage();
                    $page_no++;
                    if ($page_no === 1) {
                        $pdf->Image(base_url('assets/images/transcript.png'), 0, 0, 210, 287);
                    }
                    $left_y = $right_y = 25;
                    $y = $col_toggle === 'left' ? $left_y : $right_y;
                }

                // Draw semester header
                $pdf->SetFont('Arial', 'B', 6.5);
                $pdf->SetXY($x, $y);
                $pdf->Cell($table_width, $row_height, '', 1);
                $pdf->SetXY($x + 1.5, $y);
                $pdf->Cell(0, $row_height, "Semester: $sem", 0, 0, 'L');
                $right_text = "Session: $exam_period";
                $pdf->SetXY($x + $table_width - 1.5 - $pdf->GetStringWidth($right_text), $y);
                $pdf->SetTextColor(174, 111, 150);
                $pdf->Cell(0, $row_height, $right_text, 0, 0, 'L');
                $pdf->SetTextColor(0, 0, 0);

                $row_y = $y + $row_height;
                $pdf->SetFont('Arial', '', 5.5);
                $count = 1;

                foreach ($grouped_courses as $code => $attempts) {
                    $course_name = $attempts[0]->course_name;
                    $credits = $attempts[0]->credits_earned;

                    $fail_count = 1;
                    foreach ($attempts as $a) {
                        if (strtoupper($a->grade) === 'F') $fail_count++;
                    }

                    $final_result = ($fail_count === 1) ? 'P' : 'P#' . $fail_count;
                    $last_grade = strtoupper(end($attempts)->grade);
                    if ($fail_count > 1) {
                        $credits = strtoupper(end($attempts)->credits_earned);
                    }

                    $pdf->SetXY($x, $row_y);
                    $pdf->Cell(2.5, $row_height, $count++, 1, 0, 'C');
                    $pdf->Cell(70, $row_height, $course_name, 1, 0, 'L');
                    $pdf->Cell(5, $row_height, $credits, 1, 0, 'C');
                    $pdf->Cell(5, $row_height, $last_grade, 1, 0, 'C');
                    $pdf->Cell(7, $row_height, $final_result, 1, 0, 'C');
                    $row_y += $row_height;
                }

                // Draw SGPA/CGPA/footer line
                $pdf->SetFont('Arial', 'B', 6);
                $sgpa = number_format($sem_data[0]->sgpa ?? 0, 2);
                $cgpa = number_format($sem_data[0]->cgpa ?? 0, 2);
                $result = 'PASS';
                $pdf->SetXY($x, $row_y);
                $pdf->Cell($table_width, $row_height, "SGPA: $sgpa     CGPA: $cgpa                            $result", 1);
                $row_y += $row_height;

                // Update next Y position
                if ($col_toggle === 'left') {
                    $left_y = $row_y;
                    $col_toggle = 'right';
                } else {
                    $right_y = $row_y;
                    $col_toggle = 'left';
                }
            }

            // ✅ After all semesters — print footer
            $footer_y = max($left_y, $right_y);
            $buffered_footer_y = $footer_y + 5;
            $pdf->SetXY(10, $buffered_footer_y);
            $pdf->Cell(0, 3, '# Cleared in Subsequent Exams', 0, 1);
            $pdf->Cell(0, 3, 'P- Passed in Credit Mandatory Course', 0, 1);
            $pdf->Cell(0, 3, 'PP- Passed in Non Credit Mandatory Course', 0, 1);

            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetXY(10, $footer_y + 12);
            $pdf->Cell(190, 3, 'Authentic', 0, 1, 'R');

            $pdf->SetXY(10, $footer_y + 25);
            $issue_date = date('d-M-Y'); // Example: 15-May-2025
            $pdf->Cell(63, 3, 'Issue Date: ' . $issue_date . '     Checked By', 0, 0, 'L');
            $pdf->Cell(64, 3, 'Controller of Examinations', 0, 0, 'C');
            $pdf->Cell(63, 3, 'Principal', 0, 1, 'R');






            $pdf->Output($student->usn . '_transcript.pdf', 'I');
        } else {
            redirect('admin/timeout');
        }
    }
}
