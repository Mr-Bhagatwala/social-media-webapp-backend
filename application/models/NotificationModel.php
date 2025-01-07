<?php
class NotificationModel extends CI_Model {

    public function __construct(){ 
        parent::__construct(); 
        $this->load->database();  
    } 
    // Get notifications for a user
    public function getNotifications($userId) {
        $this->db->where('user_id', $userId);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('notifications')->result_array();
    }

    // Mark a notification as read
    public function markAsRead($notificationId) {
        $data = ['is_read' => 1];
        $this->db->where('id', $notificationId);
        return $this->db->update('notifications', $data);
    }

    // Add a new notification
    public function addNotification($data) {
        return $this->db->insert('notifications', $data);
    }
    public function addNotificationforPost($data) {
        $frds = $data['user_ids'];

        foreach ($frds as $frd){
            $notificationdata =[
                'user_id'=> $frd,
                'message'=> "Your connections share a new post !! Click to see now : "
            ];
            $this->db->insert('notifications', $notificationdata);
        }
        return $this->db->affected_rows();
    }
}
?>
