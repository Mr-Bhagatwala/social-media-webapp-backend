<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkDetailsModel extends CI_Model {

    // Add work details
    public function fillWorkDetails($company_organisation, $designation, $start_year, $end_year, $is_current) {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return false;
        }

        $data = array(
            'user_id' => $user_id,
            'company_organisation' => $company_organisation,
            'designation' => $designation,
            'start_year' => $start_year,
            'end_year' => $end_year,
            'is_current' => $is_current
        );

        return $this->db->insert('work_history', $data);
    }

    // Remove work details
    // public function removeWorkDetails($id) {
    //     $user_id = $this->session->userdata('user_id');

    //     if (!$user_id) {
    //         echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
    //         return false;
    //     }

    //     // Check if the work detail belongs to the logged-in user
    //     $this->db->where('id', $id);
    //     $this->db->where('user_id', $user_id);

    //     if ($this->db->delete('work_history')) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    // Update work details
    public function updateWorkDetails($id, $company_organisation, $designation, $start_year, $end_year, $is_current) {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return false;
        }

        $data = array(
            'company_organisation' => $company_organisation,
            'designation' => $designation,
            'start_year' => $start_year,
            'end_year' => $end_year,
            'is_current' => $is_current
        );

        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);

        return $this->db->update('work_history', $data);
    }
}
?>
