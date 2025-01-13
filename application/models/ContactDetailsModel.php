<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ContactDetailsModel extends CI_Model {

    public function fillContactDetails($primary_phone, $linkedin_url ,$user_id) {
        // Get user_id from session
        // $user_id = $this->session->userdata('user_id');

        // if (!$user_id) {
        //     echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
        //     return;
        // }
        
        // Prepare the data for insertion
        $data = array(
            'user_id' => $user_id,
            'alternative_phone' => $primary_phone,
            'linkedin_url' => $linkedin_url,
        );

        $this->db->insert('contact_details', $data);
        return ['status' => 'success', 'message' => 'Added in contact table'];
        // Check if contact details already exist for the user
        // $this->db->where('user_id', $user_id);
        // $query = $this->db->get('contact_details');

        // if ($query->num_rows() > 0) {
        //     // If contact details exist, update the existing record
        //     // $this->db->where('user_id', $user_id);
        //     // return ['status' => 'success', 'message' => 'Contact details worked'];
            
        // } else {
        //     // If no contact details exist, insert a new record
        //     $this->db->insert('contact_details', $data);
        //     return ['status' => 'error', 'message' => 'Error in contact details'];
            
        // }
    }

    public function getUserContactHistory($user_id){
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('contact_details');
        return $query->result_array();
    }

    public function updateContactDetails($user_id, $phone, $linkedin_url){
        $data = [
            'user_id' => $user_id,
            'primary_phone' => $phone,
            'linkedin_url' => $linkedin_url,
        ];

        $this->db->where('user_id', $user_id);
        return $this->db->update('contact_details', $data); // Update only basic details
    }
}
?>
