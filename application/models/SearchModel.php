<?php
class SearchModel extends CI_Model {
    public function __construct(){ 
        parent::__construct(); 
        $this->load->database();  
    } 
    // Search users by query
    public function searchUsers($query) {
        $this->db->like('name', $query);
        $this->db->or_like('email', $query);
        return $this->db->get('users')->result_array();
    }
}
?>
