<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Auth_Model'); // Load the model
        $this->load->library('form_validation'); // Load form validation library
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(200);  // Respond with HTTP OK status
            exit;  // Terminate the script after the preflight response
        }
        header('Access-Control-Allow-Origin: *');  // Allow all origins
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');  // Allow these methods
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        $this->load->helper('cookie');
    }
    
    public function check_session()
    {
        $user_id = $this->session->userdata('user_id');
        if ($user_id) {
            echo json_encode(['status' => 'success', 'user_id' => $user_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No user ID found in session.']);
        }
    }

    public function checkUser(){
        echo json_encode(['status' => 'SADSF', 'message' => 'FSDLGJSK']);
    }

    // Handle registration form submission
    public function register_user()
    {
        // Validation for registration form
         $postData = json_decode(file_get_contents('php://input'), true); // Get JSON input
        // $this->form_validation->set_rules('name', 'Name', 'required');
        // $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        // $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
    
        // if ($this->form_validation->run() == FALSE) {
        //     echo json_encode(['status' => 'error', "data is "=> $postData,'message' => validation_errors()]);
        //     return;
        // }
    
        // Get user data
        $name = $postData['name']; // Correct variable name
        $email = $postData['email']; // Correct variable name
        $password = $postData['password']; // Correct variable name
        // $email = $this->input->post('email');
        // $password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
    
        // Save user to database
        $user_id = $this->Auth_Model->register($name, $email, $password);
    
        if ($user_id) {
            // Store user_id in session
            $this->session->set_userdata('user_id', $user_id);
            
            // Send success response
            echo json_encode(['status' => 'success', 'message' => 'User registered successfully.','userId' => $user_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to register user.']);
        }
    }

    // Handle login form submission
    public function login_user()
    {
        // Decode JSON input
        $postData = json_decode(file_get_contents('php://input'), true);
        // Extract email and password from postData
        $email = $postData['email'];
        $password = $postData['password'];
        // Check if user exists using the Auth_model
        $user = $this->Auth_Model->login($email);

    
        if ($user) {
            // Use password_verify for secure password checking
            if ($password==$user['password']) {
                // Set session data for logged-in user

                $this->session->set_userdata('user_id', $user['id']);
                $this->session->set_userdata('user_email', $user['email']);
                $this->session->set_userdata('user_name', $user['name']);
                
                // Set cookie (example configuration)
                $cookie = array(
                    'name'   => 'user_id',
                    'value'  => $user['id'],
                    'expire' => '3600', // 1 hour
                    'path'   => '/',
                    'secure' => FALSE, // Set to TRUE if using HTTPS
                    'httponly' => TRUE
                );
                $this->input->set_cookie($cookie);
                // Return success message
                echo json_encode(['status' => 'success', 'message' => "User logged in successfully", "user"=>$user]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid email or user not found',
                'email' => $email,
                'password' => $password,
                'user' => $user
            ]);
        }
    }
    
    public function editBasicDetails(){
        $inputData = json_decode(file_get_contents('php://input'), true);
        if (!isset($inputData['user_id']) || !isset($inputData['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
            return;
        }

        $result = $this->Auth_Model->updateBasicDetails($inputData['user_id'], $inputData['name'], $inputData['bio'], $inputData['hometown']);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Details updated successfully', 'updatedData' => $result]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update details']);
        }
    }

    // public function editBasicDetails(){

    // }
    // public function editBasicDetails(){

    // }
    
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
        
        // Load the file upload library
        $this->load->library('upload');
        $data = json_decode(file_get_contents('php://input'), true);
        // $data = $this->input->post();
    
    
        // Update user details
        $update_data = array(
            'profile_photo' =>"",
            'gender' => $data['gender'],
            'marital_status' => $data['marital_status'],
            'date_of_birth' => $data['date_of_birth'],
            'current_city' => $data['current_city'],
            'hometown' => $data['hometown']
        );
        
        $this->db->where('id', $data['user_id']);
        $result = $this->db->update('users', $update_data);
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update profile.']);
        }
    }
    
    public function updateEmail(){
        // $user_id = $this->input->get()
        // $this->load->library('upload');
        $data = json_decode(file_get_contents('php://input'), true);
        $update_data = array(
            'email' => $data['pEmail']
        );
        
        $this->db->where('id', $data['user_id']);
        $result = $this->db->update('users', $update_data);
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Email updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update email.']);
        }
    }
    
    public function searchUsers($name) {
        $result = $this->Auth_Model->searchUsersM($name);
        echo json_encode($result);
    }
    
    public function getUser(){
        $user_id = $this->input->get('id');
        
        // $user_id = json_decode(file_get_contents('php://input'), true); This is needed when we send data inside body
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }
        $user = $this->Auth_Model->getUserDetail($user_id);
        echo json_encode(['status' => 'success', 'user' => $user]);
    }

}
?>
