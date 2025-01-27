
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
    

}
?>