<?php
class PostModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Create a new post
    public function createPost($userId, $content, $mediaUrls) {
        $this->db->trans_start();

        // Insert into posts table
        $this->db->insert('posts', ['user_id' => $userId, 'content' => $content]);
        $postId = $this->db->insert_id();

        // Insert into media table
        foreach ($mediaUrls as $url) {
            $this->db->insert('media', ['post_id' => $postId, 'media_url' => $url]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            return ['status' => 'success', 'message' => 'Post created successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to create post'];
        }
    }

    // Delete a post
    public function deletePost($postId, $userId) {
        $this->db->where('post_id', $postId);
        $this->db->delete('media');  // Assuming the media table is called 'media'
    
        // Then, delete the post
        $this->db->where('post_id', $postId);
        // $this->db->where('user_id', $userId);
        return $this->db->delete('posts');
    
        // Commit the transaction
      //  $this->db->trans_complete();
     
    }

    // Get feed with pagination
    public function getFeed($offset, $sort) {
        $this->db->select('p.post_id, p.content, p.created_at, u.name, GROUP_CONCAT(m.media_url) as media');
        $this->db->from('posts p');
        $this->db->join('users u', 'u.user_id = p.user_id');
        $this->db->join('media m', 'm.post_id = p.post_id', 'left');
        $this->db->group_by('p.post_id');

        if ($sort === 'recent') {
            $this->db->order_by('p.created_at', 'DESC');
        } else {
            $this->db->order_by('p.created_at', 'ASC');
        }

        $this->db->limit(10, $offset);
        return $this->db->get()->result_array();
    }

    // Like a post
    public function likePost($postId, $userId) {
        $this->db->insert('likes', ['post_id' => $postId, 'user_id' => $userId]);
        if ($this->db->affected_rows() > 0) {
            return ['status' => 'success', 'message' => 'Post liked successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to like post'];
        }
    }

    // Add a comment
    public function addComment($commentData) {
        $this->db->insert('comments', $commentData);
        if ($this->db->affected_rows() > 0) {
            return ['status' => 'success', 'message' => 'Comment added successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to add comment'];
        }
    }

    public function getCommentsofPost($postId){
        $this->db->where('post_id', $postId);
        
        return $this->db->get('comments')->result_array();
        
    }
    // Get notifications
    public function getNotifications($userId) {
        $this->db->select('message, link, is_read, created_at');
        $this->db->where('user_id', $userId);
        return $this->db->get('notifications')->result_array();
    }
}
?>
