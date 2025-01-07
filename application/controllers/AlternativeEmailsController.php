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
        // $user_id = $this->session->userdata('user_id');

        // if (!$user_id) {
        //     echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
        //     return;
        // }

        // $data = $this->input->post();
        $data = json_decode(file_get_contents('php://input'), true);
        // $this->form_validation->set_rules('alternative_email', 'Alternative Email', 'required|valid_email');

        // if ($this->form_validation->run() == FALSE) {
        //     $response = array('status' => 'error', 'message' => validation_errors());
        //     echo json_encode($response);
        //     return;
        // }

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

    public function removeAlternativeEmail($id) {
        // Attempt to delete the record
        $this->db->where('id', $id);
        $this->db->delete('alternative_emails');
    
        // Check the number of affected rows
        if ($this->db->affected_rows() > 0) {
            // If a row was deleted
            echo json_encode(['status' => 'success', 'message' => 'Alternative Email removed successfully.']);
        } else {
            // If no rows were affected (id does not exist in the database)
            echo json_encode(['status' => 'error', 'message' => 'ID not found in the database.']);
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