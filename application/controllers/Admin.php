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

			$data['students'] = $this->admin_model->getDetails('courses', $id)->result();
			// var_dump($data['students']); die();

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

public function students()
{
	if ($this->session->userdata('logged_in')) {
		$session_data = $this->session->userdata('logged_in');
		$data['id'] = $session_data['id'];
		$data['username'] = $session_data['username'];
		$data['full_name'] = $session_data['full_name'];
		$data['role'] = $session_data['role'];

		$data['page_title'] = "Students";
		$data['menu'] = "students";

		$data['students'] = $this->admin_model->getDetails('students', $id)->result();
		// var_dump($data['students']); die();

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
			$data['programme_options'] = array(" " => "Select Year") + $this->globals->programme();
			$data['branch_options'] = array(" " => "Select Branch") + $this->globals->branch();

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
		$data['programme_options'] = array(" " => "Select Year") + $this->globals->programme();
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

}
