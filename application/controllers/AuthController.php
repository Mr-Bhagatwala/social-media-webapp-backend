<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AuthController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(200);  // Respond with HTTP OK status
            exit;  // Terminate the script after the preflight response
        }
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        $this->load->database(); // Load the database
        $this->load->model('Auth_Model'); // Load the model
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('Auth_Model'); // Load the model
        $this->load->library('form_validation'); // Load form validation library
        $this->load->helper('cookie');
        $this->load->library('email');

        $this->SMTPUser = getenv('EMAIL_USER');
        $this->SMTPPass = getenv('EMAIL_PASS');
        $this->SMTPHost = getenv('EMAIL_HOST');
        $this->SMTPPort = getenv('EMAIL_PORT');
        $this->SMTPName = getenv('EMAIL_NAME');
        $this->SMTPProtocol = getenv('EMAIL_PROTOCOL');
        $this->SMTPType = getenv('EMAIL_TYPE');
        $this->SMTPAuth = getenv('EMAIL_AUTH');
        $this->SMTPTls = getenv('EMAIL_TLS');




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

    public function checkUser()
    {
        echo json_encode(['status' => 'SADSF', 'message' => 'FSDLGJSK']);
    }

    // Handle registration form submission
    public function register_user()
    {
        // Get JSON input
        $postData = json_decode(file_get_contents('php://input'), true);

        // Get user data
        $name = $postData['name'];
        $email = $postData['email'];
        $password = password_hash($postData['password'], PASSWORD_DEFAULT);

        $this->load->library('email');


        // Email Configuration
        $config = array(
            'protocol' => $this->SMTPProtocol,
            'smtp_host' => $this->SMTPHost, // Replace with your SMTP server
            'smtp_user' => $this->SMTPUser,
            'smtp_pass' => $this->SMTPPass,
            'smtp_port' => $this->SMTPPort, // SMTP port
            'mailtype' => $this->SMTPType,
            'charset' => 'utf-8',
            'wordwrap' => TRUE,
            'newline' => "\r\n"
        );
        $this->email->initialize($config);


        // Set email parameters
        $this->email->from('smtp_user', $this->SMTPName);
        $this->email->to($email);
        $this->email->subject('Welcome to Our Platform!');
        $this->email->message("<h3>Hi $name,</h3><p>Thank you for registering! Your account has been successfully created.</p>");

        // Send email
        // Save user to database
        $user_id = $this->Auth_Model->register($name, $email, $password);


        if ($this->email->send()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'User registered successfully. Email sent.',
                'userId' => $user_id
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'User registered successfully, but email sending failed.',
                'error' => $this->email->print_debugger()
            ]);
        }

        if (!$user_id) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to register user.'
            ]);
            return;
        }

        // Store user_id in session
        $this->session->set_userdata('user_id', $user_id);

        // Load the email library


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
            // if ($password==$user['password']) {
            if (password_verify($password, $user['password'])) {
                // Set session data for logged-in user

                // $this->session->set_userdata('user_id', $user['user_id']);
                // $this->session->set_userdata('user_email', $user['email']);
                // $this->session->set_userdata('user_name', $user['name']);

                // Set cookie (example configuration)
                $cookie = array(
                    'name' => 'user_id',
                    'value' => $user['id'],
                    'expire' => '3600', // 1 hour
                    'path' => '/',
                    'secure' => FALSE, // Set to TRUE if using HTTPS
                    'httponly' => TRUE
                );
                $this->input->set_cookie($cookie);
                // Return success message
                echo json_encode(['status' => 'success', 'message' => "User logged in successfully", "user" => $user]);
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

    // Handle login with OTP  form submission
    public function generateOtp()
    {
        $postData = json_decode(file_get_contents('php://input'), true);
        $email = $postData['email'];


        if (empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Email is required']);
            return;
        }

        $user = $this->Auth_Model->login($email);
        if ($user) {

            $otp = rand(100000, 999999);

            $this->Auth_Model->saveOtp($email, $otp);

            $this->load->library('email');

            $config = array(
                'protocol' => $this->SMTPProtocol,
                'smtp_host' => $this->SMTPHost, // Replace with your SMTP server
                'smtp_user' => $this->SMTPUser,
                'smtp_pass' => $this->SMTPPass,
                'smtp_port' => $this->SMTPPort, // SMTP port
                'mailtype' => $this->SMTPType,
                'charset' => 'utf-8',
                'wordwrap' => TRUE,
                'newline' => "\r\n"
            );
            $this->email->initialize($config);

            $this->email->from('smtp_user', $this->SMTPName);
            $this->email->to($email);
            $this->email->subject('Your OTP Code');
            $this->email->message("<p>Your OTP is <strong>$otp</strong>.</p>");

            if ($this->email->send()) {
                echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully on user mail ID ', 'showBtn' => true]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP on user mail ID ']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
    }


    public function verifyOtp()
    {
        $postData = json_decode(file_get_contents('php://input'), true);
        $email = $postData['email'];
        $otp = $postData['otp'];

        if (empty($email) || empty($otp)) {
            echo json_encode(['status' => 'error', 'message' => 'Email and OTP are required']);
            return;
        }
        $user = $this->Auth_Model->login($email);
        // Check OTP in the database
        if ($user) {
            if ($this->Auth_Model->verifyOtp($email, $otp)) {
                $cookie = array(
                    'name' => 'user_id',
                    'value' => $user['id'],
                    'expire' => '3600', // 1 hour
                    'path' => '/',
                    'secure' => FALSE, // Set to TRUE if using HTTPS
                    'httponly' => TRUE
                );
                $this->input->set_cookie($cookie);

                echo json_encode(['status' => 'success', 'message' => 'OTP verified and user logged in successfully', "user" => $user]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid OTP']);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid email or user not found',
                'email' => $email,
                'user' => $user
            ]);
        }
    }

    public function editBasicDetails()
    {
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
    public function addProfileDetails()
    {

        // Load the file upload library
        $this->load->library('upload');
        $data = json_decode(file_get_contents('php://input'), true);
        // $data = $this->input->post();


        // Update user details
        $update_data = array(
            'profile_photo' => "",
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

    public function updateEmail()
    {
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

    public function searchUsers($name)
    {
        $result = $this->Auth_Model->searchUsersM($name);
        echo json_encode($result);
    }

    // public function fetchUsers(){
    //     $users = $this->Auth_Model->getAllUsers();
    //     echo json_encode([
    //         'success' => true,
    //         'data' => $users
    //     ]);
    // }

    public function fetchUsers()
    {
        // Load input data for offset and limit
        $offset = $this->input->get('offset', true);
        $limit = $this->input->get('limit', true);
        $search = $this->input->get('search') ?: '';


        // Provide default values if offset and limit are not provided
        $offset = $offset !== null ? (int) $offset : 0;
        $limit = $limit !== null ? (int) $limit : 10;

        // Fetch users from the model with pagination
        $users = $this->Auth_Model->getAllUsers($offset, $limit, $search);

        // Return the response
        if (!empty($users)) {
            echo json_encode([
                'success' => true,
                'data' => $users
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No more users found'
            ]);
        }
    }


    public function uploadPhoto()
    {
        $userId = $this->input->post('user_id');

        if (!$userId) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'User ID is required']));
        }

        if (empty($_FILES['profile_photo']['name'])) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'No file uploaded']));
        }

        $config['upload_path'] = './assets/profile_pictures/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 5120;
        $config['file_name'] = 'profile_' . $userId . '_' . uniqid();

        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('profile_photo')) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]));
        }

        $uploadData = $this->upload->data();
        $filePath = 'assets/profile_pictures/' . $uploadData['file_name'];

        $updateStatus = $this->Auth_Model->updateProfilePhoto($userId, $filePath);

        if ($updateStatus) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'photo_url' => base_url($filePath)]));
        } else {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to update profile photo in the database']));
        }

    }

    public function getUser()
    {
        $user_id = $this->input->get('id');

        // $user_id = json_decode(file_get_contents('php://input'), true); This is needed when we send data inside body
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }
        $user = $this->Auth_Model->getUserDetail($user_id);

        $this->load->model('ContactDetailsModel');
        $contactDetails = $this->ContactDetailsModel->getUserContactHistory($user_id);

        $this->load->model('AlternativeEmailsModel');
        $alternateEmails = $this->AlternativeEmailsModel->getUserEmails($user_id);

        $this->load->model('AlternativePhonesModel');
        $alternatePhones = $this->AlternativePhonesModel->getUserPhones($user_id);

        $this->load->model('EducationDetailsModel');
        $education = $this->EducationDetailsModel->getUserEducationHistory($user_id);

        $this->load->model('WorkDetailsModel');
        $work = $this->WorkDetailsModel->getUserWorkHistory($user_id);

        $response = [
            'status' => 'success',
            'user' => $user[0], // Assuming getUserDetail returns a single row
            'contact_details' => $contactDetails,
            'alternate_emails' => $alternateEmails,
            'alternate_phones' => $alternatePhones,
            'education' => $education,
            'work' => $work,
        ];

        echo json_encode(['status' => 'success', 'data' => $response]);
    }
}
?>