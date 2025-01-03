<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ContactController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('ContactDetailsModel');
        $this->load->helper('url');
        $this->load->library('form_validation');
    }

      // Add contact details
      public function addContactDetails() {
        
        $data = json_decode(file_get_contents("php://input"), true);

        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('primary_phone', 'Primary Phone', 'required');
        $this->form_validation->set_rules('linkedin_url', 'LinkedIn URL', 'required|valid_url');
        $this->form_validation->set_rules('alternative_email', 'Alternative Email', 'required|valid_email');
        $this->form_validation->set_rules('alternative_phone', 'Alternative Phone', 'required');

        if ($this->form_validation->run() == FALSE) {
            $response = array('status' => 'error', 'message' => validation_errors());
            echo json_encode($response);
            return;
        }

        $result = $this->ContactDetailsModel->fillContactDetails(
            $data['primary_phone'], 
            $data['linkedin_url'], 
            $data['alternative_email'], 
            $data['alternative_phone']
        );

        if ($result) {
            $response = array('status' => 'success', 'message' => 'Contact details added successfully.');
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to add contact details.');
        }
        echo json_encode($response);
    }
}
?>