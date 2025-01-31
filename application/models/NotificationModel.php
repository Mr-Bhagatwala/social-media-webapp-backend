<?php

class NotificationModel extends CI_Model {

    // public function __construct(){ 
    //     parent::__construct(); 
    //     $this->load->database();  
    // } 
    // // Get notifications for a user
    // // public function getNotifications($userId) {
    // //     $this->db->where('user_id', $userId);
    // //     $this->db->order_by('created_at', 'DESC');
    // //     return $this->db->get('notifications')->result_array();
    // // }
    // public function getNotifications($userId) {
    //     // Select notifications along with the source's name and profile_photo
    //     $this->db->select('
    //         notifications.*, 
    //         source_users.name AS name, 
    //         source_users.profile_photo AS profile_photo
    //     ');
    
    //     // Join with the users table (alias as source_users) to get source user details (name and profile_photo)
    //     $this->db->from('notifications');
    //     $this->db->join('users AS source_users', 'source_users.id = notifications.source_id', 'left'); // Left join on source_id
    
    //     // Apply the condition for the specific user
    //     $this->db->where('notifications.user_id', $userId);
    
    //     // Order the notifications by creation date in descending order
    //     $this->db->order_by('notifications.created_at', 'DESC');
    
    //     // Get the result and return as an array
    //     return $this->db->get()->result_array();
    // }
    
    

    // // Mark a notification as read
    // public function markAsRead($notificationId) {
    //     $data = ['is_read' => 1];
    //     $this->db->where('id', $notificationId);
    //     return $this->db->update('notifications', $data);
    // }

    // // Add a new notification
    // public function addNotification($data) {
    //     return $this->db->insert('notifications', $data);
    // }
    // public function addNotificationforPost($data) {
    //     $frds = $data['user_ids'];

    //     foreach ($frds as $frd){
    //         $notificationdata =[
    //             'user_id'=> $frd,
    //             'message'=> "Your connections share a new post !! Click to see now : "
    //         ];
    //         $this->db->insert('notifications', $notificationdata);
    //     }
    //     return $this->db->affected_rows();
    // }
    
    public function __construct(){ 
        parent::__construct(); 
        $this->load->database();  
    } 
    // Get notifications for a user
    // public function getNotifications($userId) {
    //     $this->db->where('user_id', $userId);
    //     $this->db->order_by('created_at', 'DESC');
    //     return $this->db->get('notifications')->result_array();
    // }
    public function getNotifications($userId, $offset, $limit) {
        // Select notifications with user details
        $this->db->select('
            notifications.*, 
            source_users.name AS name, 
            source_users.profile_photo AS profile_photo
        ');
        $this->db->from('notifications');
        $this->db->join('users AS source_users', 'source_users.id = notifications.source_id', 'left');
        $this->db->where('notifications.user_id', $userId);
        $this->db->order_by('notifications.created_at', 'DESC');
        $this->db->limit($limit, $offset); // Apply limit and offset
    
        return $this->db->get()->result_array();
    }
    
    //delete notification
    public function deleteNotificationsByUserId($userId) {
        $this->db->where('id', $userId);
        return $this->db->delete('notifications'); // Replace 'notifications' with your actual table name
    }
    public function deleteAllNotificationsByUserId($userId) {
        $this->db->where('user_id', $userId);
        return $this->db->delete('notifications'); // Replace 'notifications' with your actual table name
    }
    
    

    // Mark a notification as read
    public function markAsRead($notificationId) {
        $data = ['is_read' => 1];
        $this->db->where('id', $notificationId);
        return $this->db->update('notifications', $data);
    }

    public function markAsAllRead($user_id) {
        // Check if there are any notifications for the user
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('notifications');
        if ($query->num_rows() > 0) {
            $data = ['is_read' => 1];
            $this->db->where('user_id', $user_id);
            return $this->db->update('notifications', $data);
        } else {
            return false; 
        }
    }

    // Add a new notification
    public function addNotification($data) {
        return $this->db->insert('notifications', $data);
    }
    public function addNotificationforPost($data, $srcId) {
        $frds = $data['user_ids'];

        foreach ($frds as $frd){
            $notificationdata =[
                'user_id'=> $frd,
                'message'=> "Your connections share a new post !! Click to see now : ",
                'source_id'=> $srcId
            ];
            $this->db->insert('notifications', $notificationdata);
        }
         if( $this->db->affected_rows()){

            return ["status"=>"success", "message"=>"Post created succesfully"];
         }else{
            return ["status"=>"failed", "message"=>"Not able to send notifications || No friends"];
         }
    }
}
?>

