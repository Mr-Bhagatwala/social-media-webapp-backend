<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChatController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('ChatModel');

        // CORS headers for all requests
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    public function getAllChats($userId) {
        if ($userId == null || !is_numeric($userId)) {
            $response = [
                'status' => 'error',
                'message' => 'User ID is required.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
    
        $data = $this->ChatModel->getAllChats($userId);
    
        if ($data === null || empty($data)) { // Check if data is null or empty
            $response = [
                'status' => 'error',
                'message' => 'No chats found or something went wrong.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
    
        $response = [
            'status' => 'success',
            'message' => 'Chats fetched successfully',
            'data' => $data
        ];
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    //mute a chat
    public function muteChat($userId, $chatId) {
        if ($userId == null || !is_numeric($userId)) {
            $response = [
                'status' => 'error',
                'message' => 'User ID is required.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }
            if ($chatId == null || !is_numeric($chatId)) {
                $response = [
                    'status' => 'error',
                    'message' => 'Chat ID is required.'
                ];
                return $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }
            $response = $this->ChatModel->muteChat($userId, $chatId);
            if ($response === false) { // Check if the chat was muted successfully
                $response = [
                    'status' => 'error',
                   'message' => 'Failed to mute chat.'
                ];
                return $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }
            $response = [
                'status' => 'success',
                'message' => 'Chat muted successfully.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
        // unmute a chat
    public function unmuteChat($userId, $chatId) {
            if ($userId == null || !is_numeric($userId)) {
                $response = [
                    'status' => 'error',
                   'message' => 'User ID is required.'
                ];
                return $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }
            if ($chatId == null || !is_numeric($chatId)) {
                $response = [
                    'status' => 'error',
                   'message' => 'Chat ID is required.'
                ];
                return $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }
            $response = $this->ChatModel->unmuteChat($userId, $chatId);
            if ($response === false) { // Check if the chat was unmuted successfully
                $response = [
                    'status' => 'error',
                   'message' => 'Failed to unmute chat.'
                ];
                return $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }
            $response = [
                'status' => 'success',
                'message' => 'Chat unmuted successfully.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    // i have a table blocked_users in which there are 2 cols 1 blocker_id and 2 blocked_id 
    // block user 
    public function blockUser($userId, $blockedUserId) {
        if ($userId == null || !is_numeric($userId)) {
            $response = [
                'status' => 'error',
                'message' => 'User ID is required.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
        if ($blockedUserId == null || !is_numeric($blockedUserId)) {
            $response = [
                'status' => 'error',
                'message' => 'Blocked User ID is required.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
        $response = $this->ChatModel->blockUser($userId, $blockedUserId);
        if ($response === false) { // Check if the user was blocked successfully
            $response = [
                'status' => 'error',
                'message' => 'Failed to block user.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
        $response = [
            'status' => 'success',
           'message' => 'User blocked successfully.'
        ];
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    // unblock user
    public function unblockUser($userId, $blockedUserId) {
        if ($userId == null || !is_numeric($userId)) {
            $response = [
                'status' => 'error',
                'message' => 'User ID is required.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
        if ($blockedUserId == null || !is_numeric($blockedUserId)) {
            $response = [
                'status' => 'error',
                'message' => 'Blocked User ID is required.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
        $response = $this->ChatModel->unblockUser($userId, $blockedUserId);
        if ($response === false) { // Check if the user was unblocked successfully
            $response = [
                'status' => 'error',
                'message' => 'Failed to unblock user.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
        $response = [
            'status' => 'success',
           'message' => 'User unblocked successfully.'
        ];
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    //clear chat data
    public function clearChat($chatId) {
        if ($chatId == null || !is_numeric($chatId)) {
            $response = [
                'status' => 'error',
                'message' => 'Chat ID is required.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
        $response = $this->ChatModel->clearChat($chatId);
        if ($response === false) { // Check if the chat data was cleared successfully
            $response = [
                'status' => 'error',
               'message' => 'Failed to clear chat data.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
        $response = [
            'status' => 'success',
           'message' => 'Chat data cleared successfully.'
        ];
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    //delete chat data
    public function deleteChat($chatId) {
        if ($chatId == null || !is_numeric($chatId)) {
            $response = [
                'status' => 'error',
                'message' => 'Chat ID is required.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
        $response = $this->ChatModel->deleteChatData($chatId);
        if ($response === false) { // Check if the chat data was deleted successfully
            $response = [
                'status' => 'error',
               'message' => 'Failed to delete chat data.'
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
        $response = [
            'status' => 'success',
           'message' => 'Chat data deleted successfully.'
        ];
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
}
?>
