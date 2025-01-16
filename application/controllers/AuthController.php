<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');

        $this->load->model('Auth_model'); // Load the model
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



    // Handle registration form submission
    // public function register_user()
    // {
    //     // Validation for registration form
    //      $postData = json_decode(file_get_contents('php://input'), true); // Get JSON input
    //     $this->form_validation->set_rules('name', 'Name', 'required');
    //     $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
    //     $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
    
    //     // if ($this->form_validation->run() == FALSE) {
    //     //     echo json_encode(['status' => 'error', "data is "=> $postData,'message' => validation_errors()]);
    //     //     return;
    //     // }
    
    //     // Get user data
    //     $name = $this->input->post('name'); // Correct variable name
    //     $email = $this->input->post('email');
    //     $password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
    
    //     // Save user to database
    //     $user_id = $this->Auth_model->register($name, $email, $password);
    
    //     if ($user_id) {
    //         // Store user_id in session
    //         $this->session->set_userdata('user_id', $user_id);
    
    //         // Send success response
    //         echo json_encode(['status' => 'success', 'message' => 'User registered successfully.']);
    //     } else {
    //         echo json_encode(['status' => 'error', 'message' => 'Failed to register user.']);
    //     }
    // }


    

    public function register_user()
    {
        // Decode JSON input
        $postData = json_decode(file_get_contents('php://input'), true);
    
        // Debug incoming data
        log_message('debug', 'Incoming data: ' . print_r($postData, true));
    
        // Basic manual validation
        if (empty($postData['name']) || empty($postData['email']) || empty($postData['password'])) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            return;
        }
    
        // Ensure email is valid
        if (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
            return;
        }

        // $user = $this->Auth_model->login($postData['email']);
        // if($user){
        //     echo json_encode(['status' => 'error', 'message' => 'already email address is registered.'],"user" => $user);
        //     return;
        // }
    
        // Ensure password length is sufficient
        if (strlen($postData['password']) < 6) {
            echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters long.']);
            return;
        }
    
        // Extract user data
        $name = $postData['name'];
        $email = $postData['email'];
        $password = $postData['password'];
    
        // Save user to database
        $user_id = $this->Auth_model->register($name, $email, $password);
         
        if ($user_id) {
            // Store user_id in session
            $this->session->set_userdata('user_id', $user_id);
            // Send success response
            echo json_encode(['status' => 'success', 'message' => 'User registered successfully.', 'user_id'  =>  $user_id] 
           );
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
        $user = $this->Auth_model->login($email);

    
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
        // Retrieve user_id from cookie
        // $user_id =  $this->input->get_request_header('user_id', TRUE);// TRUE for XSS filtering
    
        // if (!$user_id) {
        //     echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
        //     return;
        // }
    
        // Load the file upload library
        $this->load->library('upload');
        $data = json_decode(file_get_contents('php://input'), true);
        // $data = $this->input->post();
    
        // Uncomment and use validation rules if necessary
        // $this->load->library('form_validation');
        // $this->form_validation->set_rules('gender', 'Gender', 'required');
        // $this->form_validation->set_rules('marital_status', 'Marital Status', 'required');
        // $this->form_validation->set_rules('date_of_birth', 'Date of Birth', 'required');
        // $this->form_validation->set_rules('current_city', 'Current City', 'required');
        // $this->form_validation->set_rules('hometown', 'Hometown', 'required');
    
        // Check if profile picture is uploaded
        // $profile_photo_path = '';
        // if (!empty($_FILES['profile_photo']['name'])) {
        //     // Set upload configuration
        //     $config['upload_path'] = './assets/profile_pictures/';
        //     $config['allowed_types'] = 'gif|jpg|jpeg|png';
        //     $config['max_size'] = 2048;
    
        //     $this->upload->initialize($config);
    
        //     // Perform the upload
        //     if (!$this->upload->do_upload('profile_photo')) {
        //         echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
        //         return;
        //     }
    
        //     // Get the uploaded file data
        //     $upload_data = $this->upload->data();
        //     $profile_photo_path = 'assets/profile_pictures/' . $upload_data['file_name'];
        // } else {
        //     $profile_photo_path = isset($data['profile_photo']) ? $data['profile_photo'] : '';
        // }
    
        // Run form validation
        // if ($this->form_validation->run() == FALSE) {
        //     echo json_encode(['status' => 'error', 'message' => validation_errors()]);
        //     return;
        // }
    
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
    
    public function getUser(){
        // $user_id = $this->input->get('user_id');
        $user_id = json_decode(file_get_contents('php://input'), true);
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }
        $user = $this->Auth_model->getUserDetail($user_id);
        echo json_encode(['status' => 'success', 'user' => $user]);
    
    }
}
?>
