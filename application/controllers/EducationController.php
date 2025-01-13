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
        $data = json_decode(file_get_contents('php://input'), true);
    
        // Validate required fields
        if (!$data['user_id'] || !$data['college_school'] || !$data['degree_program'] || !$data['start_year']) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
            return;
        }

        // Check if the same degree already exists for the user
        $this->db->where('user_id', $data['user_id']);
        $this->db->where('degree_program', $data['degree_program']);
        $existingRecord = $this->db->get('education')->row();
        
        $educationData = [
            'college_school' => $data['college_school'],
            'degree_program' => $data['degree_program'],
            'start_year' => $data['start_year'],
            'end_year' => $data['is_current'] ? null : $data['end_year'],
            'is_current' => $data['is_current']
        ];
    
        if ($existingRecord) {
            $this->db->where('id', $existingRecord->id);
            $updated = $this->db->update('education', $educationData);
            
            if ($updated) {
                echo json_encode(['status' => 'success', 'message' => 'Education details updated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update education details.']);
            }
        } else {
            // Insert a new record
            $educationData['user_id'] = $data['user_id'];
            $inserted = $this->db->insert('education', $educationData);
    
            if ($inserted) {
                echo json_encode(['status' => 'success', 'message' => 'Education details added successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save education details.']);
            }
        }
    }
    
    // Remove education details
    public function removeEducationDetails() {
        $data = json_decode(file_get_contents('php://input'), true);
    
        // Validate required fields
        if (!$data['user_id'] || !$data['degree_program']) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields: user_id or degree_program.']);
            return;
        }
        
        // Delete the education record matching user_id and degree_program
        $this->db->where('user_id', $data['user_id']);
        $this->db->where('degree_program', $data['degree_program']);
        $this->db->delete('education');
    
        if ($this->db->affected_rows() > 0) {
            // Successfully deleted
            echo json_encode(['status' => 'success', 'message' => 'Education details removed successfully.']);
        } else {
            // No matching record found
            echo json_encode(['status' => 'error', 'message' => 'No matching education details found to delete.']);
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
