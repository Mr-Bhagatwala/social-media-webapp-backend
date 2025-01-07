
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');  // Allow all origins
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');  // Allow these methods
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
class NotificationController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->model('NotificationModel');
         // For input validation
    }

    public function getNotificationofUser($userId){
        //from param
        // $userId = $this->input->get('user_id');
        $response = $this->NotificationModel->getNotifications($userId);

        if($response){
            return $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
        }
        return $this->output->set_content_type('application/json')
        ->set_output(json_encode(["status"=> "failed", "message"=>"No Notifications "]));
    }

}