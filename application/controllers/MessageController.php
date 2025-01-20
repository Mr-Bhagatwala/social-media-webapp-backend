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

        $messages = $this->MessageModel->getPastMessagesByChat($chatId);
        return $this->output->set_content_type('application/json')->set_output(json_encode($messages));
    }

    public function sendMessage(){
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['chat_id']) || empty($data['sender_id']) || empty($data['message_text'])) {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(400)
                        ->set_output(json_encode(['error' => 'chat_id, sender_id, and message_text are required']));
        }

        $messageData = [
            'chat_id' => $data['chat_id'],
            'sender_id' => $data['sender_id'],
            'message_text' => $data['message_text'],
            'message_type' => isset($data['message_type']) ? $data['message_type'] : 'text',
            'file_url' => isset($data['file_url']) ? data['file_url'] : null,
            'timestamp' => date('Y-m-d H:i:s'),
            'deleted' => 0, 
        ];

        $message = $this->MessageModel->insertMessage($messageData);

        if ($message) {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode(['success' => 'Message sent successfully']));
        } else {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(500)
                        ->set_output(json_encode(['error' => 'Failed to send message']));
        }
    }

    public function deleteMessage() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['message_id'])) {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(400)
                        ->set_output(json_encode(['error' => 'message_id is required']));
        }

        $deleted = $this->MessageModel->deleteMessageById($data['message_id']);

        if ($deleted) {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(200)
                        ->set_output(json_encode(['success' => 'Message deleted successfully']));
        } else {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(500)
                        ->set_output(json_encode(['error' => 'Failed to delete message']));
        }
    }

    public function replyToMessage(){
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['chat_id']) || empty($data['sender_id']) || empty($data['message_text']) || empty($data['parent_message_id'])) {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(400)
                        ->set_output(json_encode(['error' => 'chat_id, sender_id, message_text, and parent_message_id are required']));
        }

        $messageData = [
            'chat_id' => $data['chat_id'],
            'sender_id' => $data['sender_id'],
            'message_text' => $data['message_text'],
            'message_type' => isset($data['message_type']) ? $data['message_type'] : 'text',
            'file_url' => isset($data['file_url']) ? data['file_url'] : null,
            'timestamp' => date('Y-m-d H:i:s'),
            'parent_message_id' => $data['parent_message_id']
        ];

        $insertedId = $this->MessageModel->insertMessage($messageData);

        if ($insertedId) {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(200)
                        ->set_output(json_encode(['success' => 'Reply sent successfully', 'message_id' => $insertedId]));
        } else {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(500)
                        ->set_output(json_encode(['error' => 'Failed to send reply']));
        }
    }
}

?>