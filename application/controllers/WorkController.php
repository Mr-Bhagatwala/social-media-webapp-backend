<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('WorkDetailsModel'); // Load the model
        $this->load->library('form_validation'); // Load form validation library
    }

     // Add work details
     public function addWorkDetails() {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }

        $data = $this->input->post();

        $this->form_validation->set_rules('company_organisation', 'Company_Organisation', 'required');
        $this->form_validation->set_rules('designation', 'Designation', 'required');
        $this->form_validation->set_rules('start_year', 'Start Year', 'required');
        $this->form_validation->set_rules('end_year', 'End Year', 'required');
        $this->form_validation->set_rules('is_current', 'Is Current', 'required');

        if ($this->form_validation->run() == FALSE) {
            $response = array('status' => 'error', 'message' => validation_errors());
            echo json_encode($response);
            return;
        }

        $result = $this->WorkDetailsModel->fillWorkDetails(
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

    public function removeWorkDetails($id) {
        // Attempt to delete the record
        $this->db->where('id', $id);
        $this->db->delete('work_history');
    
        // Check the number of affected rows
        if ($this->db->affected_rows() > 0) {
            // If a row was deleted
            echo json_encode(['status' => 'success', 'message' => 'Work details removed successfully.']);
        } else {
            // If no rows were affected (id does not exist in the database)
            echo json_encode(['status' => 'error', 'message' => 'ID not found in the database.']);
        }
    }
    
    public function updateWorkDetails($id) {
        $data = $this->input->post();

        $this->form_validation->set_rules('company_organisation', 'Company Organisation', 'required');
        $this->form_validation->set_rules('designation', 'Designation', 'required');
        $this->form_validation->set_rules('start_year', 'Start Year', 'required');
        $this->form_validation->set_rules('end_year', 'End Year', 'required');
        $this->form_validation->set_rules('is_current', 'Is Current', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        $this->db->where('id', $id);
        $query = $this->db->get('work_history');  // Assuming the table is named 'education_details'
    
        if ($query->num_rows() == 0) {
            // If no record is found with the given ID
            echo json_encode(['status' => 'error', 'message' => 'work details not found.']);
            return;
        }
        $result = $this->WorkDetailsModel->updateWorkDetails(
            $id,
            $data['company_organisation'],
            $data['designation'],
            $data['start_year'],
            $data['end_year'],
            $data['is_current']
        );

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Work details updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update work details.']);
        }
    }

}
?>