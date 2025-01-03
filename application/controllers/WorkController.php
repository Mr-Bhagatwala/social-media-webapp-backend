<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model'); // Load the model
        $this->load->library('form_validation'); // Load form validation library
    }

     // Add work details
     public function addWorkDetails() {
        $data = json_decode(file_get_contents("php://input"), true);

        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('company_organisation', 'Company/Organisation', 'required');
        $this->form_validation->set_rules('designation', 'Designation', 'required');
        $this->form_validation->set_rules('start_year', 'Start Year', 'required');
        $this->form_validation->set_rules('end_year', 'End Year', 'required');
        $this->form_validation->set_rules('is_current', 'Is Current', 'required');

        if ($this->form_validation->run() == FALSE) {
            $response = array('status' => 'error', 'message' => validation_errors());
            echo json_encode($response);
            return;
        }

        $result = $this->ContactDetailsModel->fillWorkDetails(
            $data['company_organisation'], 
            $data['designation'], 
            $data['start_year'], 
            $data['end_year'], 
            $data['is_current']
        );

        if ($result) {
            $response = array('status' => 'success', 'message' => 'Work details added successfully.');
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to add work details.');
        }
        echo json_encode($response);
    }

}
?>