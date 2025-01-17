
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

    public function getNotificationofUser($userId){
        //from param
        // $userId = $this->input->get('user_id');
        if (empty($uId) || !is_numeric($uId)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid or missing User ID.']));
        }

        $response = $this->NotificationModel->getNotifications($userId);
        foreach($response as &$friend){
            $friend['profile_photo'] = base_url().$friend['profile_photo'];
        }

        if($response){
            return $this->output->set_content_type('application/json')
            ->set_output(json_encode(["data"=>$response, "status"=>"success"]));
        }
        return $this->output->set_content_type('application/json')
        ->set_output(json_encode(["status"=> "success", "message"=>"No Notifications "]));
    }

}
?>