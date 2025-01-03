<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ContactController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('ContactDetailsModel');
        $this->load->helper('url');
        $this->load->library('form_validation');
    }

    // Add or update contact details
    public function addContactDetails() {

        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }

        $data = $this->input->post();
        
        $this->form_validation->set_rules('primary_phone', 'Primary Phone', 'required');
        $this->form_validation->set_rules('linkedin_url', 'LinkedIn URL', 'required|valid_url');
        $this->form_validation->set_rules('alternative_email', 'Alternative Email', 'required|valid_email');
        $this->form_validation->set_rules('alternative_phone', 'Alternative Phone', 'required');

        if ($this->form_validation->run() == FALSE) {
            $response = array('status' => 'error', 'message' => validation_errors());
            echo json_encode($response);
            return;
        }

        // Call the model method to add or update the contact details
        $result = $this->ContactDetailsModel->fillContactDetails(
            $data['primary_phone'], 
            $data['linkedin_url'], 
            $data['alternative_email'], 
            $data['alternative_phone']
        );

        // Check the result and respond accordingly
        if ($result === 'updated') {
            $response = array('status' => 'success', 'message' => 'Contact details updated successfully.');
        } elseif ($result === 'added') {
            $response = array('status' => 'success', 'message' => 'Contact details added successfully.');
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to add or update contact details.');
        }

        echo json_encode($response);
    }
}
?>
