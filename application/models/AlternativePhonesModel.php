<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AlternativePhonesModel extends CI_Model {
    public function addAlternativePhone($user_id, $alternative_phone) {
        $data = [
            'user_id' => $user_id,
            'alternate_phone' => $alternative_phone,
        ];
        // $this->db->where('user_id', $user_id);
        return $this->db->insert('alternate_phones', $data);
    }

    public function removeAlternativePhone($id, $user_id) {
        // Ensure only the user's own phone can be removed
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        return $this->db->delete('alternate_phones');
    }

    public function getUserPhones($user_id) {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('alternate_phones');
        return $query->result_array();
    }
}
?>
