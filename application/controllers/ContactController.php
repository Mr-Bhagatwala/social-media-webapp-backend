<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ContactController extends CI_Controller {
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('ContactDetailsModel'); 
        $this->load->helper('url');
        $this->load->library('form_validation'); 
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(200); 
            exit;  
        }
        header('Access-Control-Allow-Origin: *');  
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');  
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        $this->load->helper('cookie');
        
    }

    // Add or update contact details
    public function addContactDetails() {

        $data = json_decode(file_get_contents('php://input'), true);

        $update_data = array(
            'user_id' => $data['user_id'],
            'primary_phone' => $data['primary_phone'],
            'linkedin_url' => $data['linkedin_url'],
        );

        $result = $this->db->insert('contact_details', $update_data);
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Contact updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update contact.']);
        }
       
    }

    public function listContactDetails(){
        $user_id = json_decode(file_get_contents('php://input'), true);
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }

        $contactDetails = $this->ContactDetailsModel->getUserContactHistory($user_id);
        echo json_encode(['status' => 'success', 'contact' => $contactDetails]);
    }

    public function checkContact(){
        echo json_encode(['status' => 'success', 'contact' => 'hello']);
    }

    public function editContactDetails(){
        $inputData = json_decode(file_get_contents('php://input'), true);
        $result = $this->ContactDetailsModel->updateContactDetails($inputData['user_id'], $inputData['primaryPhone'], $inputData['linkedinUrl']);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Details updated successfully', 'updatedData' => $result]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update details']);
        }
    }
}
?>
