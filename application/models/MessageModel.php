<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MessageModel extends CI_Model {

    public function __construct() {
        parent::__construct(); // Call the parent constructor
    }
    
    public function getPastMessagesByChat($chatId) {
        $this->db->select('*');
        $this->db->from('messages');
        $this->db->where('chat_id', $chatId);
        $this->db->where('deleted', false);
        $this->db->order_by('timestamp', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function insertMessage($data) {
        return $this->db->insert('messages', $data);
    }
    
    public function deleteMessageById($messageId) {
        $this->db->where('message_id', $messageId);
        $this->db->delete('messages');
        return $this->db->affected_rows() > 0;
    }
    
    // public function insertMessage($data){
    //     $this->db->insert('messages', $data);
    //     return $this->db->insert_id();

    // }

}

?>