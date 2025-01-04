<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model'); // Load the model
        $this->load->library('form_validation'); // Load form validation library
        header('Access-Control-Allow-Origin: *');  // Allow all origins
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');  // Allow these methods
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    // Handle registration form submission
    public function register_user()
    {
        // Validation for registration form
       json_decode(file_get_contents('php://input'), true); // Get JSON input
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
    
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
    
        // Get user data
        $name = $this->input->post('name'); // Correct variable name
        $email = $this->input->post('email');
        $password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
    
        // Save user to database
        $user_id = $this->Auth_model->register($name, $email, $password);
    
        if ($user_id) {
            // Store user_id in session
            $this->session->set_userdata('user_id', $user_id);
    
            // Send success response
            echo json_encode(['status' => 'success', 'message' => 'User registered successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to register user.']);
        }
    }
    

    // Handle login form submission
    public function login_user()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');
    
        if ($this->form_validation->run() == FALSE)
        {
            // Validation failed, return errors
            echo json_encode(['status' => 'error', 'message' => "ahiya error ave 6"]);
            return;
        }
        
        // Get email and password
        $email = $this->input->post('email');
        $password = $this->input->post('password');
    
        // Check if user exists
        $user = $this->Auth_model->login($email); // Corrected from $this->user to $this->Auth_model
    
        if ($user)
        {
            // Check password using password_verify
            if (password_verify($password, $user['password']))
            {
                // Set session data for logged-in user
                $this->session->set_userdata('user_id', $user['id']);
                $this->session->set_userdata('user_email', $user['email']);
                $this->session->set_userdata('user_name', $user['name']);
    
                // Return success message
                echo json_encode(['status' => 'success', 'message' => "User logged in successfully"]);
            }
            else
            {
                // Invalid password
                echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
            }
        }
        else
        {
            // User not found
            echo json_encode(['status' => 'error', 'message' => 'Invalid email or user not found']);
        }
    }
    

    // Logout
    public function logout()
    {
        // Check if the user is logged in
        $user_id = $this->session->userdata('user_id');
        
        if ($user_id) {
            // Unset all session data
            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('user');
            
            // Destroy the session
            $this->session->sess_destroy();
    
            // Respond with a success message
            echo json_encode(['status' => 'success', 'message' => 'You have been logged out successfully.']);
        } else {
            // User is not logged in, send an error message
            echo json_encode(['status' => 'error', 'message' => 'No active session found.']);
        }
    
        // Optionally, redirect the user to the login page after logging out
        // redirect('/login');
    }
    

    // Add profile details
    public function addProfileDetails() {
        $user_id = $this->session->userdata('user_id');
    
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }
    
        // Load the file upload library
        $this->load->library('upload');
    
        $data = $this->input->post();
        
        // Add validation rules
        $this->form_validation->set_rules('gender', 'Gender', 'required');
        $this->form_validation->set_rules('marital_status', 'Marital Status', 'required');
        $this->form_validation->set_rules('date_of_birth', 'Date of Birth', 'required');
        $this->form_validation->set_rules('current_city', 'Current City', 'required');
        $this->form_validation->set_rules('hometown', 'Hometown', 'required');
    
        // Check if profile picture is uploaded
        if (!empty($_FILES['profile_photo']['name'])) {
            // Set upload configuration
            $config['upload_path'] = './assests/profile_pictures/';  // Path where the file will be saved
            $config['allowed_types'] = 'gif|jpg|jpeg|png';  // Allowed file types
            $config['max_size'] = 2048;  // Maximum file size (2MB)
    
            $this->upload->initialize($config);
    
            // Perform the upload
            if (!$this->upload->do_upload('profile_photo')) {
                // If upload fails, return error
                echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                return;
            }
    
            // Get the uploaded file data
            $upload_data = $this->upload->data();
            $profile_photo_path = 'uploads/profile_pictures/' . $upload_data['file_name'];  // Store the file path
        } else {
            $profile_photo_path = $data['profile_photo'];  // If no file uploaded, use existing profile photo
        }
    
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
    
        // Update user details
        $update_data = array(
            'profile_photo' => $profile_photo_path,
            'gender' => $data['gender'],
            'marital_status' => $data['marital_status'],
            'date_of_birth' => $data['date_of_birth'],
            'current_city' => $data['current_city'],
            'hometown' => $data['hometown']
        );
        
        $this->db->where('id', $user_id); // Ensure 'id' is the correct column name for user identification
        $result = $this->db->update('users', $update_data); // Update the user profile
    
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update profile.']);
        }
    }
    
}
?>
