<?php  
defined('BASEPATH') OR exit('No direct script access allowed');

class AlternativeEmailsModel extends CI_Model {
    public function addAlternativeEmail($alternative_email,$user_id) {
        
        $data = array(
            'user_id' => $user_id,
            'alternate_email' => $alternative_email,
        );

        if ($this->db->insert('alternate_emails', $data)) {
            return ['status' => 'success', 'message' => 'Alternate email added successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to add alternative email.'];
        }
    }

    public function removeAlternativeEmail($id, $user_id) {
        // Ensure only the user's own phone can be removed
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        return $this->db->delete('alternate_phones');
    }

    public function getUserEmails($user_id) {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('alternate_emails');
        return $query->result_array();
    }
}
?>
