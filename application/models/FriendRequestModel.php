<?php
class FriendRequestModel extends CI_Model {

    // Send Friend Request
    public function __construct(){ 
        parent::__construct(); 
        $this->load->database();  
    } 
    public function sendRequest($data) {
        //add validation on data
        if($data == null){
            return null;
        }
        return $this->db->insert('friend_requests', $data);
    }

    // Get all Friend Requests for a user (pending )
    // public function getRequests($userId, $type = 'pending') {
    //     $this->db->where('receiver_id', $userId);
    //     $this->db->where('status', $type);
    //     return $this->db->get('friend_requests')->result_array();
    // }
    public function getRequests($userId, $type = 'pending') {
        // Select friend request details along with the sender's name and profile photo
        //add validation on userId
        if($userId == null){
            return null;
        }
        $this->db->select('
            friend_requests.*, 
            sender_users.name AS name, 
            sender_users.profile_photo AS profile_photo
        ');
    
        // Join with the users table (alias as sender_users) to get sender details (name and profile_photo)
        $this->db->from('friend_requests');
        $this->db->join('users AS sender_users', 'sender_users.id = friend_requests.sender_id', 'inner'); // Inner join on sender_id
    
        // Apply the conditions for the specific user and the request status
        $this->db->where('friend_requests.receiver_id', $userId);
        $this->db->where('friend_requests.status', $type);
    
        // Get the result and return as an array
        return $this->db->get()->result_array();
    }
    
    public function checkExistingRequest($userId, $recieverId){
        //add validation checks
        if($userId == null || $recieverId == null){
            return null;
        }
        $this->db->where('sender_id', $userId);
        $this->db->where('receiver_id', $recieverId);
        return $this->db->get('friend_requests')->result_array();
    }

    // Respond to Friend Request (accept/reject)
    public function respondRequest($sender_id, $receiver_id, $status) {

        //add validation checks
        if($sender_id == null || $receiver_id == null || $status == null){
            return null;
        }
        $data = ['status' => $status];
        $this->db->where('sender_id', $sender_id);
        $this->db->where('receiver_id', $receiver_id);
        return $this->db->update('friend_requests', $data);
    }

    public function getRequestbyId($requestId){
        //add validation checks
        if($requestId == null){
            return null;
        }
        $this->db->where('id', $requestId);
        return $this->db->get('friend_requests')->result_array();
    }
    // Get the Friends list of a user
    public function getFriendsList($userId) {
        //add validation checks
        if($userId == null){
            return null;
        }
        // Get friends where the user is the receiver
        $this->db->select('users.id as friend_id, users.name, users.profile_photo');
        $this->db->from('friend_requests');
        $this->db->join('users', 'friend_requests.sender_id = users.id', 'inner');
        $this->db->where('friend_requests.receiver_id', $userId);
        $this->db->where('friend_requests.status', 'accepted');
        $receiverFriends = $this->db->get()->result_array();
    
        // Get friends where the user is the sender
        $this->db->select('users.id as friend_id, users.name, users.profile_photo');
        $this->db->from('friend_requests');
        $this->db->join('users', 'friend_requests.receiver_id = users.id', 'inner');
        $this->db->where('friend_requests.sender_id', $userId);
        $this->db->where('friend_requests.status', 'accepted');
        $senderFriends = $this->db->get()->result_array();
    
        // Combine both sets of friends
        $friendsList = array_merge($receiverFriends, $senderFriends);
    
        return $friendsList;
    }
    public function deleterequest($sender_id, $receiver_id){
        //add validation checks
        if($sender_id == null || $receiver_id == null){
            return null;
        }
        $this->db->where('receiver_id',$receiver_id);
        $this->db->where('sender_id',$sender_id);
        
        return $this->db->delete('friend_requests');
        
    }

    public function getFriendRequest($sender_id, $receiver_id) {
        //add validation checks
        if($sender_id == null || $receiver_id == null){
            return null;
        }
        $this->db->select('status'); // Select only the status column
        $this->db->where('receiver_id', $receiver_id);
        $this->db->where('sender_id', $sender_id);
        $query = $this->db->get('friend_requests'); // Assuming 'friend_requests' is your table name
    
        if ($query->num_rows() > 0) {
            return $query->row()->status; // Return the status directly
        } else {
            return null; // No request found
        }
    }
    
    // Add a user to the Friends table
    // public function addFriend($data) {
    //     return $this->db->insert('friends', $data);
    // }
}
?>
