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

	function courses()
	{
		if ($this->session->userdata('logged_in')) {
			$session_data = $this->session->userdata('logged_in');
			$data['id'] = $session_data['id'];
			$data['username'] = $session_data['username'];
			$data['full_name'] = $session_data['full_name'];
			$data['role'] = $session_data['role'];

			$data['page_title'] = "Courses";
			$data['menu'] = "students";

			$data['currentAcademicYear'] = $this->globals->currentAcademicYear();
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

			// Set validation rules
			$this->form_validation->set_rules('course_code', 'Course Code', 'required');
			$this->form_validation->set_rules('course_name', 'Course Name', 'required');
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
					$this->session->set_flashdata('message', 'Course Details added successfully!');
					$this->session->set_flashdata('status', 'alert-success');
				} else {
					$this->session->set_flashdata('message', 'Oops! Something went wrong, please try again.');
					$this->session->set_flashdata('status', 'alert-warning');
				}

				// Redirect to the same page after processing
				redirect('admin/add_newcourse', 'refresh');
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

        // Get the current course details using the provided ID
        $data['admissionDetails'] = $this->admin_model->getDetails('courses', $id)->row();

        // Load the form_validation library for validation
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        // Set form validation rules
        $this->form_validation->set_rules('course_code', 'Course Code', 'required');
        $this->form_validation->set_rules('course_name', 'Course Name', 'required');
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

}
