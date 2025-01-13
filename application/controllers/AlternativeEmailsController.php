<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class AlternativeEmailsController extends CI_Controller{
    public function __construct() {
        parent::__construct();
        $this->load->model('AlternativeEmailsModel');
        $this->load->helper('url');
        $this->load->library('form_validation');
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(200);  // Respond with HTTP OK status
            exit;  // Terminate the script after the preflight response
        }

        // CORS headers for other requests
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    public function addAlternativeEmail(){

        $data = json_decode(file_get_contents('php://input'), true);

        $result = $this->AlternativeEmailsModel->addAlternativeEmail(         
            $data['alternative_email'],
            $data['user_id']
        );
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Alternative Email added successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add  education Alternative Email.']);
        }
    }

    public function removeAlternativeEmail() {
        // Attempt to delete the record
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['user_id']) || empty($data['email'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing user_id or email.']);
            return;
        }

        $user_id = $data['user_id'];
        $email = $data['email'];

        $this->db->where('user_id', $user_id);
        $this->db->where('alternate_email', $email);
        $this->db->delete('alternate_emails');

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Alternate email deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No matching record found.']);
        }
    }

    public function listAlternativeEmails() {
        // $user_id = $this->session->userdata('user_id');
    
        // if (!$user_id) {
        //     echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
        //     return;
        // }
        
        $user_id = json_decode(file_get_contents('php://input'), true);
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }
        // Correct the variable and method names
        $emails = $this->AlternativeEmailsModel->getUserEmails($user_id); 
        echo json_encode(['status' => 'success', 'emails' => $emails]);
    }
    
}

?>