<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('WorkDetailsModel'); // Load the model
        $this->load->library('form_validation'); // Load form validation library

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

     // Add work details
     public function addWorkDetails() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data['user_id'] || !$data['company_organisation'] || !$data['designation'] || !$data['start_year']) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
            return;
        }

        $this->db->where('user_id', $data['user_id']);
        $this->db->where('company_organisation', $data['company_organisation']);
        $this->db->where('designation', $data['designation']);
        $existingRecord = $this->db->get('work_history')->row();

        $workData = [
            'company_organisation' => $data['company_organisation'],
            'designation' => $data['designation'],
            'start_year' => $data['start_year'],
            'end_year' => $data['is_current'] ? null : $data['end_year'],
            'is_current' => $data['is_current']
        ];
    
        if ($existingRecord) {
            $this->db->where('id', $existingRecord->id);
            $updated = $this->db->update('work_history', $workData);
            
            if ($updated) {
                echo json_encode(['status' => 'success', 'message' => 'Work details updated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update work details.']);
            }
        } else {
            
            $workData['user_id'] = $data['user_id'];
            $inserted = $this->db->insert('work_history', $workData);
    
            if ($inserted) {
                echo json_encode(['status' => 'success', 'message' => 'Work details added successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save work details.']);
            }
        }

    }

    public function removeWorkDetails() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data['user_id'] || !$data['company_organisation'] || !$data['designation']) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields: user_id, company_organisation, or designation.']);
            return;
        }
        
        $this->db->where('user_id', $data['user_id']);
        $this->db->where('company_organisation', $data['company_organisation']);
        $this->db->where('designation', $data['designation']);
        $this->db->delete('work_history');

        if ($this->db->affected_rows() > 0) {
            // Successfully deleted
            echo json_encode(['status' => 'success', 'message' => 'Work details removed successfully.']);
        } else {
            // No matching record found
            echo json_encode(['status' => 'error', 'message' => 'No matching work details found to delete.']);
        }
        
    }
    
    public function listWorkHistory(){
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }
        $workHistory = $this->WorkDetailsModel->getUserWorkHistory($data);
        echo json_encode(['status' => 'success', 'work' => $workHistory]);
    
    }

}
?>