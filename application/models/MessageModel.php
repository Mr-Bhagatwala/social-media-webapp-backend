<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MessageModel extends CI_Model {

    public function __construct() {
        parent::__construct(); // Call the parent constructor
    }
    
    public function getPastMessagesByChatAndUser($chatId, $userId) {
        $this->db->select('*');
        $this->db->from('messages');
        $this->db->where('chat_id', $chatId);
        $this->db->where('sender_id', $userId);
        $this->db->where('deleted', false);
        $this->db->order_by('timestamp', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }
}

?>