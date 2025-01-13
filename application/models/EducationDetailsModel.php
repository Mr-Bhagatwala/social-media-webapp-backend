<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EducationDetailsModel extends CI_Model {

    // Add education details
    public function fillEducationDetails($college_school, $degree_program, $start_year, $end_year, $is_current) {
        // Get user_id from session
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return false;
        }
        
        // Prepare the data for insertion
        $data = array(
            'user_id' => $user_id,
            'college_school' => $college_school,
            'degree_program' => $degree_program,
            'start_year' => $start_year,
            'end_year' => $end_year,
            'is_current' => $is_current
        );

        // Insert new record into education_details table
        return $this->db->insert('education', $data);
    }

    // Remove education details
    // public function removeEducationDetails($id) {
    //     $user_id = $this->session->userdata('user_id');

    //     if (!$user_id) {
    //         echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
    //         return false;
    //     }

    //     // Delete the record if it belongs to the logged-in user
    //     $this->db->where('id', $id);
    //     $this->db->where('user_id', $user_id);

    //     return $this->db->delete('education');
    // }

    // Update education details
    public function updateEducationDetails($id, $college_school, $degree_program, $start_year, $end_year, $is_current) {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return false;
        }
        
        // Prepare the data for update
        $data = array(
            'college_school' => $college_school,
            'degree_program' => $degree_program,
            'start_year' => $start_year,
            'end_year' => $end_year,
            'is_current' => $is_current
        );

        // Update the record if it belongs to the logged-in user
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);

        return $this->db->update('education', $data);
    }

    public function getUserEducationHistory($user_id){
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('education');
        return $query->result_array();
    }
}
?>
