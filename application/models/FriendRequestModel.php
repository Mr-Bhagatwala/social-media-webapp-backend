<?php
class FriendRequestModel extends CI_Model {

    // Send Friend Request
    public function __construct(){ 
        parent::__construct(); 
        $this->load->database();  
    } 
    public function sendRequest($data) {
        return $this->db->insert('friend_requests', $data);
    }

    // Get all Friend Requests for a user (pending )
    public function getRequests($userId, $type = 'pending') {
        $this->db->where('receiver_id', $userId);
        $this->db->where('status', $type);
        return $this->db->get('friend_requests')->result_array();
    }
    public function checkExistingRequest($userId, $recieverId){
        $this->db->where('sender_id', $userId);
        $this->db->where('receiver_id', $recieverId);

        return $this->db->get('friend_requests')->result_array();
    }

    // Respond to Friend Request (accept/reject)
    public function respondRequest($requestId, $status) {
        $data = ['status' => $status];
        $this->db->where('id', $requestId);
        return $this->db->update('friend_requests', $data);
    }

    public function getRequestbyId($requestId){
        $this->db->where('id', $requestId);
        return $this->db->get('friend_requests')->result_array();
    }
    // Get the Friends list of a user
    public function getFriendsList($userId) {
       
    //     $sql = "SELECT * FROM friend_requests 
    //     WHERE (sender_id = ? OR receiver_id = ?) 
    //     AND status = ?";

    // $query = $this->db->query($sql, array($userId, $userId, 'accepted'));
    // return $query->result_array();

    $sql = "SELECT sender_id AS friend_id FROM friend_requests 
        WHERE receiver_id = ? AND status = 'accepted'
        UNION
        SELECT receiver_id AS friend_id FROM friend_requests 
        WHERE sender_id = ? AND status = 'accepted'";
        
$query = $this->db->query($sql, array($userId, $userId));
    return $query->result_array();

// $friends will contain all friend IDs

    }
    public function deleterequest($requestId){
        $this->db->where('id',$requestId);
        
        return $this->db->delete('friend_requests');
        
    }
    // Add a user to the Friends table
    // public function addFriend($data) {
    //     return $this->db->insert('friends', $data);
    // }
}
?>
