<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChatModel extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    // Get all chats from database
    public function getAllChats($userId) {
        if ($userId == null || !is_numeric($userId)) {
            return null; // Return null for error cases
        }
    
        // Check if user exists
        $this->db->where('id', $userId);
        if ($this->db->count_all_results('users') == 0) {
            return null; // Return null if the user is not found
        }
    
        // Build the query
        $this->db->select('chats.chat_id, chats.sender_id, chats.receiver_id, chats.pinned, chats.last_message,
        CASE 
            WHEN chats.sender_id != ' . intval($userId) . ' THEN users.name
            ELSE receiver_user.name 
        END as name,
        CASE 
            WHEN chats.sender_id != ' . intval($userId) . ' THEN users.profile_photo
            ELSE receiver_user.profile_photo
        END as profile_photo,
        CASE 
            WHEN muted_chats.chat_id IS NOT NULL THEN 1
            ELSE 0
        END as is_muted,
        CASE 
            WHEN blocked_users.blocker_id IS NOT NULL THEN 1
            ELSE 0
        END as is_blocked', false);
        $this->db->from('chats');
        $this->db->join('users', 'chats.sender_id = users.id', 'left');
        $this->db->join('users as receiver_user', 'chats.receiver_id = receiver_user.id', 'left');
        $this->db->join('muted_chats', 'chats.chat_id = muted_chats.chat_id AND muted_chats.user_id = ' . intval($userId), 'left');
        $this->db->join('blocked_users', 'blocked_users.blocked_id = chats.receiver_id AND blocked_users.blocker_id = ' . intval($userId), 'left');
        $this->db->where("(chats.sender_id = {$userId} OR chats.receiver_id = {$userId})", null, false);
        $this->db->order_by('chats.pinned', 'DESC');
        $this->db->order_by('chats.last_message', 'DESC');
    
        $query = $this->db->get();
        return $query->result_array(); // Return the result directly
    }  
    //mute a chat
    public function muteChat($userId, $chatId) {
        if ($userId == null || !is_numeric($userId) || $chatId == null || !is_numeric($chatId)) {
            return false; // Return false for error cases
        }
        
        // Check if user exists
        $this->db->where('id', $userId);
        if ($this->db->count_all_results('users') == 0) {
            return false; // Return false if the user is not found
        }
        
        // Check if chat exists
        $this->db->where('chat_id', $chatId);
        if ($this->db->count_all_results('chats') == 0) {
            return false; // Return false if the chat is not found
        }
        
        // Update the chat's mute status
        $data = ['user_id' => $userId, 'chat_id' => $chatId];
        return $this->db->insert('muted_chats', $data); // Return the result directly
    }
    // unmute a chat
    public function unmuteChat($userId, $chatId) {
        if ($userId == null || !is_numeric($userId) || $chatId == null || !is_numeric($chatId)) {
            return false; // Return false for error cases
        }
        
        // Check if user exists
        $this->db->where('id', $userId);
        if ($this->db->count_all_results('users') == 0) {
            return false; // Return false if the user is not found
        }
        
        // Check if chat exists
        $this->db->where('chat_id', $chatId);
        if ($this->db->count_all_results('chats') == 0) {
            return false; // Return false if the chat is not found
        }
        
        // Delete the chat's mute status
        $this->db->where('user_id', $userId);
        $this->db->where('chat_id', $chatId);
        return $this->db->delete('muted_chats'); // Return the result directly
    }
    //block user
    public function blockUser($userId, $blockedUserId) {
        if ($userId == null || !is_numeric($userId) || $blockedUserId == null || !is_numeric($blockedUserId)) {
            return false; // Return false for error cases
        }
        
        // Check if user exists
        $this->db->where('id', $userId);
        if ($this->db->count_all_results('users') == 0) {
            return false; // Return false if the user is not found
        }
        
        // Check if blocked user exists
        $this->db->where('id', $blockedUserId);
        if ($this->db->count_all_results('users') == 0) {
            return false; // Return false if the blocked user is not found
        }
        
        // Check if a block already exists
        $this->db->where('blocker_id', $userId);
        $this->db->where('blocked_id', $blockedUserId);
        if ($this->db->count_all_results('blocked_users') > 0) {
            return false; // Return false if a block already exists
        }
        // Insert the block
        $data = ['blocker_id' => $userId, 'blocked_id' => $blockedUserId];
        return $this->db->insert('blocked_users', $data); // Return the result directly
    }
    // unblock user
    public function unblockUser($userId, $blockedUserId) {
        if ($userId == null || !is_numeric($userId) || $blockedUserId == null || !is_numeric($blockedUserId)) {
            return false; // Return false for error cases
        }
        
        // Check if user exists
        $this->db->where('id', $userId);
        if ($this->db->count_all_results('users') == 0) {
            return false; // Return false if the user is not found
        }
        
        // Check if blocked user exists
        $this->db->where('id', $blockedUserId);
        if ($this->db->count_all_results('users') == 0) {
            return false; // Return false if the blocked user is not found
        }
        
        // Delete the block
        $this->db->where('blocker_id', $userId);
        $this->db->where('blocked_id', $blockedUserId);
        return $this->db->delete('blocked_users'); // Return the result directly
    }
    //clear Chat data
    public function clearChat($chatId) {
        if ($chatId == null || !is_numeric($chatId)) {
            return false; // Return false for error cases
        }
        // Delete the chat's messages 
        $this->db->where('chat_id', $chatId);
        return $this->db->delete('messages'); // Return the result directly
    }
    //delete the chat
    public function deleteChatData($chatId) {
        // Validate chatId
        if (empty($chatId) || !is_numeric($chatId)) {
            return false; // Invalid chatId
        }
    
        // Begin a database transaction to ensure consistency
        $this->db->trans_start();

        //delete from muted_chats table
        $this->db->where('chat_id', $chatId);
        $this->db->delete('muted_chats');
    
        // Delete messages associated with the chat
        $this->db->where('chat_id', $chatId);
        $this->db->delete('messages');
    
        // Delete the chat record
        $this->db->where('chat_id', $chatId);
        $this->db->delete('chats');
    
        // Complete the transaction
        $this->db->trans_complete();
    
        // Check transaction status
        if ($this->db->trans_status() === false) {
            return false; // Something went wrong
        }
    
        return true; // Successfully deleted
    }
    //pin chat
    public function pinChat($chatId) {
        // Validate chatId
        if (empty($chatId) || !is_numeric($chatId)) {
            return false; // Invalid chatId
        }
        
        // Update the chat's pinned status
        $this->db->where('chat_id', $chatId);
        $this->db->update('chats', array('pinned' => 1));
        
        // Check if the update was successful
        if ($this->db->affected_rows() == 1) {
            return true; // Successfully pinned
        } else {
            return false; // Failed to pin
            }
        }
    // unpin chat
    public function unpinChat($chatId) {
        // Validate chatId
        if (empty($chatId) || !is_numeric($chatId)) {
            return false; // Invalid chatId
        }
        
        // Update the chat's pinned status
        $this->db->where('chat_id', $chatId);
        $this->db->update('chats', array('pinned' => 0));
        
        // Check if the update was successful
        if ($this->db->affected_rows() == 1) {
            return true; // Successfully unpinned
        } else {
            return false; // Failed to unpin
        }
    }
}
?>
