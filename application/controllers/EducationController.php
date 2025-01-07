<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EducationController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('EducationDetailsModel');
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

    // Add education details
    public function addEducationDetails() {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }

        $data = $this->input->post();

        $this->form_validation->set_rules('college_school', 'College/School', 'required');
        $this->form_validation->set_rules('degree_program', 'Degree Program', 'required');
        $this->form_validation->set_rules('start_year', 'Start Year', 'required');
        $this->form_validation->set_rules('end_year', 'End Year', 'required');
        $this->form_validation->set_rules('is_current', 'Is Current', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }

        $result = $this->EducationDetailsModel->fillEducationDetails(
            $data['college_school'],
            $data['degree_program'],
            $data['start_year'],
            $data['end_year'],
            $data['is_current']
        );

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Education details added successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add education details.']);
        }
    }

    // Remove education details
    public function removeEducationDetails($id) {
        $this->db->where('id',$id);
        $this->db->delete('education');
      

        if ($this->db->affected_rows() > 0) {
            // If a row was deleted
            echo json_encode(['status' => 'success', 'message' => 'education details removed successfully.']);
        } else {
            // If no rows were affected (id does not exist in the database)
            echo json_encode(['status' => 'error', 'message' => 'ID not found in the database.']);
        }
    }

    // Update education details
    public function updateEducationDetails($id) {
        $data = $this->input->post();
    
        $this->form_validation->set_rules('college_school', 'College/School', 'required');
        $this->form_validation->set_rules('degree_program', 'Degree Program', 'required');
        $this->form_validation->set_rules('start_year', 'Start Year', 'required');
        $this->form_validation->set_rules('end_year', 'End Year', 'required');
        $this->form_validation->set_rules('is_current', 'Is Current', 'required');
    
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
    
        // Check if the education details exist for the given ID
        $this->db->where('id', $id);
        $query = $this->db->get('education');  // Assuming the table is named 'education_details'
    
        if ($query->num_rows() == 0) {
            // If no record is found with the given ID
            echo json_encode(['status' => 'error', 'message' => 'Education details not found.']);
            return;
        }
    
        // If the record exists, proceed to update
        $result = $this->EducationDetailsModel->updateEducationDetails(
            $id,
            $data['college_school'],
            $data['degree_program'],
            $data['start_year'],
            $data['end_year'],
            $data['is_current']
        );
    
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Education details updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update education details.']);
        }
    }

    public function listEducationHistory(){
        $user_id = json_decode(file_get_contents('php://input'), true);
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }
        $educationHistory = $this->EducationDetailsModel->getUserEducationHistory($user_id);
        echo json_encode(['status' => 'success', 'education' => $educationHistory]);
    }
}    
?>
