<?php  
defined('BASEPATH') OR exit('No direct script access allowed');

class AlternativeEmailsModel extends CI_Model {
    public function addAlternativeEmail($alternative_email,$user_id) {
        // $user_id = $this->session->userdata('user_id');

        // // Check if the user is authenticated
        // if (!$user_id) {
        //     return ['status' => 'error', 'message' => 'User not authenticated.'];
        // }

        // Prepare the data for insertion
        $data = array(
            'user_id' => $user_id,
            'alternative_email' => $alternative_email,
        );

        // Insert the data into the database
        // $this->db->where('user_id', $user_id);
        if ($this->db->insert('alternative_emails', $data)) {
            return ['status' => 'success', 'message' => 'Alternative email added successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to add alternative email.'];
        }
    }
    public function getUserEmails($user_id) {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('alternative_emails');
        return $query->result_array();
    }
}
?>
