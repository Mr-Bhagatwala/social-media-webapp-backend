
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');  // Allow all origins
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');  // Allow these methods
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
class NotificationController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->model('NotificationModel');
        $this->load->helper('url');
         // For input validation
    }

    public function getNotificationofUser($userId) {
        // Validate User ID
        if (empty($userId) || !is_numeric($userId)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid or missing User ID.']));
        }
    
        // Get `offset` and `limit` from query parameters
        $offset = $this->input->get('offset') ?: 0; // Default to 0
        $limit = $this->input->get('limit') ?: 10; // Default to 10
    
        // Validate offset and limit
        if (!is_numeric($offset) || $offset < 0 || !is_numeric($limit) || $limit <= 0) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid offset or limit.']));
        }
    
        // Fetch notifications from the model
        $response = $this->NotificationModel->getNotifications($userId, $offset, $limit);
    
        // Add base URL to profile photos
        foreach ($response as &$notification) {
            $notification['profile_photo'] = base_url() . $notification['profile_photo'];
        }
    
        // Return response
        if ($response) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['data' => $response, 'status' => 'success']));
        }
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'No Notifications']));
    }

    public function deleteNotification(){
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id'])) {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(400)
                        ->set_output(json_encode(['error' => 'notification id  is required']));
        }

        $deleted = $this->NotificationModel->deleteNotificationsByUserId($data['id']);

        if ($deleted) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Notifications deleted successfully.']));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Failed to delete notifications or no records found.']));
        }
    }

    public function markAsReadNotification(){
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id'])) {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(400)
                        ->set_output(json_encode(['error' => 'notification id  is required']));
        }

        $deleted = $this->NotificationModel->markAsRead($data['id']);

        if ($deleted) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Notifications  set as mark as read successfully.']));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Failed to set mark as read notifications or no records found.']));
        }
    }


    public function markAsReadAllNotification() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['user_id'])) {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(400)
                        ->set_output(json_encode(['error' => 'User ID is required']));
        }
        $user_id = $data['user_id'];
        $deleted = $this->NotificationModel->markAsAllRead($user_id);
    
        if ($deleted) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'All notifications set as read successfully.']));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'No notifications found for this user or failed to update.']));
        }
    }


    public function deleteAllNotification() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['user_id'])) {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(400)
                        ->set_output(json_encode(['error' => 'User ID is required']));
        }
        $user_id = $data['user_id'];
        $deleted = $this->NotificationModel->deleteAllNotificationsByUserId($user_id);
    
        if ($deleted) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'All notifications delete successfully.']));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'No notifications found for this user or failed to update.']));
        }
    }
    

}
?>