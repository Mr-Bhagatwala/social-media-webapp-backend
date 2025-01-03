<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model'); // Load the model
        $this->load->library('form_validation'); // Load form validation library
    }

    // Handle registration form submission
    public function register_user()
{
    // Validation for registration form
    $this->form_validation->set_rules('name', 'name', 'required');
    $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');

    if ($this->form_validation->run() == FALSE)
    {
        // // If validation fails, return an error message or reload the register view
        // $this->load->view('register');  // Assuming you have a register view
        // // You can also display validation errors like this:
        echo validation_errors();
    }
    else
    {
        // Get user data from form
        $username = $this->input->post('name');
        $email = $this->input->post('email');
        $password = password_hash($this->input->post('password'), PASSWORD_DEFAULT); // Hash password

        // Save user to database
        $is_registered = $this->Auth_model->register($username, $email, $password);

        if ($is_registered)
        {
            // Redirect to login page or dashboard on success
            $this->session->set_flashdata('success', 'Registration successful! Please log in.');
            // redirect('/login');  // Or any other page where user can log in
        }
        else
        {
            // Show error message if registration failed
            $this->session->set_flashdata('error', 'Registration failed. Please try again.');
            redirect('/register');  // Go back to register page or another page for retry
        }
    }
}



    // Handle login form submission
    public function login_user()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE)
        {
            // $this->load->view('login');
        }
        else
        {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            // Check if user exists
            $user = $this->Auth_model->login($email);

            if ($user && password_verify($password, $user['password']))
            {
                // Set session data for logged-in user
                $this->session->set_userdata('user', $user);
                echo " logged in";

                // redirect('dashboard');
            }
            else
            {
                $this->session->set_flashdata('error', 'Invalid email or password');
                redirect('/');
            }
        }
    }

    // Logout
    public function logout()
    {
        $this->session->unset_userdata('user');
        // redirect('/login');
    }

        // Add profile details
    public function addProfileDetails() {
        $data = json_decode(file_get_contents("php://input"), true);

        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('profile_photo', 'Profile Photo', 'required');
        $this->form_validation->set_rules('gender', 'Gender', 'required');
        $this->form_validation->set_rules('marital_status', 'Marital Status', 'required');
        $this->form_validation->set_rules('date_of_birth', 'Date of Birth', 'required');
        $this->form_validation->set_rules('current_city', 'Current City', 'required');
        $this->form_validation->set_rules('hometown', 'Hometown', 'required');

        if ($this->form_validation->run() == FALSE) {
            $response = array('status' => 'error', 'message' => validation_errors());
            echo json_encode($response);
            return;
        }

        $result = $this->ContactDetailsModel->fillDetails(
            $data['profile_photo'], 
            $data['gender'], 
            $data['marital_status'], 
            $data['date_of_birth'], 
            $data['current_city'], 
            $data['hometown']
        );

        if ($result) {
            $response = array('status' => 'success', 'message' => 'Profile details added successfully.');
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to add profile details.');
        }
        echo json_encode($response);
    }

}
?>
