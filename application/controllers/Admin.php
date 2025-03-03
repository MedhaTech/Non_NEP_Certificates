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

    public function courses() {
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
    
                // Convert "All" options to NULL (so they don’t filter anything)
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

    public function students() {
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

        // Clear any previous output to prevent header issues
        if (ob_get_length()) {
            ob_end_clean();
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
                $pdf->Cell(80, $row_height, $course->course_name, 1, 0, 'L', true);
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

            // CGPA and Date
            $pdf->SetXY(155, min(188, $pdf->GetPageHeight() - 15));
            $pdf->Cell(50, 10, $semester_data[0]->cgpa ?? '-', 0, 1);

            $pdf->SetXY(78, min(188, $pdf->GetPageHeight() - 15));
            $pdf->Cell(50, 10, date('d-M-Y'));

            // Handle result year safely
            $result_year = $course->result_year ?? '1970-01-01';
            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetXY(201, 67.2);
            $pdf->Cell(50, 10, date('F Y', strtotime($result_year)), 0, 1);

            // Generate Barcode
            if (!empty($barcode_number)) {
                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                $barcode = $generator->getBarcode($barcode_number, $generator::TYPE_CODE_128);

                $barcodePath = APPPATH . 'temp/barcode_' . $barcode_number . '.png';
                file_put_contents($barcodePath, $barcode);

                // Add barcode image
                $pdf->Image($barcodePath, 19, 174, 50, 10);

                // Delete temp barcode file
                unlink($barcodePath);
            }
        }

        // Send PDF to browser
        ob_clean(); // Clean output buffer again to prevent errors
        $pdf->Output('D', $semester . ' semester Grade Card' . '.pdf');
        exit(); // Ensure script stops after PDF is generated
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
                $sgpa = number_format($semester_data[0]->sgpa ?? 0, 2);
                $cgpa = number_format($semester_data[0]->cgpa ?? 0, 2);                
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


    public function forgot_password_view()
    {
        $this->login_template->show('admin/forgot_password_view');
    }
    

   public function forgot_password() {
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

    $token = bin2hex(random_bytes(20));//token generation
    $expiry_time = date("Y-m-d H:i:s", strtotime('+5 minutes'));//set expire date and time 5 min

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
    $this->email->from('roreplayreplay@gmail.com', 'BMSCE CERTIFY 2008'); 
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
//     $this->email->from('nandeeshjkalakatti@gmail.com', 'BMSCE CERTIFY 2008'); 
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

public function reset_password() {
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

public function update_password() {
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




public function edit_marks($courseid , $stuid) {
    // Check if the user is logged in
    

    if ($this->session->userdata('logged_in')) {
        // Get POST data from the form
        $data = $this->input->post();
        
        // Validate the input data
        $this->form_validation->set_rules('course_code', 'Course Code', 'required');
        // Add other validation rules as necessary

        if ($this->form_validation->run() == FALSE) {
            // If validation fails, redirect back with error messages
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            // Prepare the data for updating
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

            // Call model to update the course details
            $result = $this->admin_model->update_marks($courseid, $updateDetails);

            // Flash message for successful or failed update
            if ($result) {
                $this->session->set_flashdata('message', 'Marks updated successfully!');
                $this->session->set_flashdata('status', 'alert-success');
            } else {
                $this->session->set_flashdata('message', 'Something went wrong, please try again!');
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


public function deletemarks($id  , $stuid) {
    if ($this->session->userdata('logged_in')) {
        // Call the model to delete the course
        $result = $this->admin_model->deletemarks($id);

        if ($result) {
            $this->session->set_flashdata('message', 'Course deleted successfully!');
            $this->session->set_flashdata('status', 'alert-success');
        } else {
            $this->session->set_flashdata('message', 'Error deleting course. Please try again.');
            $this->session->set_flashdata('status', 'alert-danger');
        }
        // Redirect back to the student details page or wherever appropriate
        $encryptId = base64_encode($stuid);
        redirect('admin/studentdetails/' . $encryptId);// Adjust the redirect as necessary
    } else {
        redirect('admin');
    }
}






    
}
