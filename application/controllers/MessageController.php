<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MessageController extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MessageModel');
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(200); 
            exit; 
        }
        header('Access-Control-Allow-Origin: *');  
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');  
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    public function getPastMessages() {
        // $userId = $this->input->get('userId');
        $chatId = $this->input->get('chatId');

        if (empty($chatId)) {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(400)
                        ->set_output(json_encode(['error' => 'chatId is required']));
        }

        $messages = $this->MessageModel->getPastMessagesByChatAndUser($chatId);
        return $this->output->set_content_type('application/json')->set_output(json_encode($messages));
    }
}

?>