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
        $this->db->delete('media');  
    
        // Then, delete the post
        $this->db->where('post_id', $postId);
        // $this->db->where('user_id', $userId);
        return $this->db->delete('posts');
    
        // Commit the transaction
      //  $this->db->trans_complete();
     
    }

    // Get feed with pagination
    public function getFeed($offset, $sort) {
        $this->db->select('p.post_id, p.content, p.created_at, u.name, u.profile_photo, GROUP_CONCAT(m.media_url) as media');
        $this->db->from('posts p');
        $this->db->join('users u', 'u.id = p.user_id');
        $this->db->join('media m', 'm.post_id = p.post_id', 'left');
        $this->db->group_by('p.post_id');

        if ($sort === 'recent') {
            $this->db->order_by('p.created_at', 'DESC');
        } else {
            $this->db->order_by('p.created_at', 'ASC');
        }

        $this->db->limit(3, $offset);
        return $this->db->get()->result_array();
    }

    // Like a post
    public function likePost($postId, $userId) {
        $this->db->insert('likes', ['post_id' => $postId, 'user_id' => $userId]);
        if ($this->db->affected_rows() > 0) {
            return ['status' => 'success', 'message' => 'added'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to like post'];
        }
    }

    public function removelike($post_id, $user_id){
        $this->db->delete('likes', ['post_id' => $post_id, 'user_id' => $user_id]);
        return ['status' => 'success', 'message' => 'removed'];
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

    public function getCommentsofPost($postId) {
        $this->db->select('comments.*, users.name as user_name, users.profile_photo');
        $this->db->from('comments');
        $this->db->join('users', 'comments.user_id = users.id', 'left'); // Adjust 'users.id' to your actual user ID column name
        $this->db->where('comments.post_id', $postId);
        return $this->db->get()->result_array();
    }
    // Get notifications
    public function getNotifications($userId) {
        $this->db->select('message, link, is_read, created_at');
        $this->db->where('user_id', $userId);
        return $this->db->get('notifications')->result_array();
    }
    // get no of likes with post id

    public function getLikesCount($postId){
        $this->db->where('post_id', $postId);
        $this->db->from('likes');
        return $this->db->count_all_results();
    }
    // get no of commnet with post id 

    public function getCommentsCount($postId){
        $this->db->where('post_id', $postId);
        $this->db->from('comments');
        return $this->db->count_all_results();
    }
    //get user posts
    public function isLikeByUser($postId, $userId){
        $this->db->where('post_id', $postId);
        $this->db->where('user_id', $userId);
        
        return $this->db->get('likes')->row();
    }

    public function getUserPost($userId){
        // $this->db->where('user_id', $userId);

        // return $this->db->get('posts')->result_array();
        $this->db->select('p.post_id, p.content, p.created_at, u.profile_photo, u.name, u.id, GROUP_CONCAT(m.media_url) as media');
        $this->db->from('posts p');
        
        $this->db->join('users u', 'u.id = p.user_id');
        $this->db->join('media m', 'm.post_id = p.post_id', 'left');
       
        $this->db->group_by('p.post_id');
        $this->db->where('u.id', $userId);

        return $this->db->get()->result_array();
    }
}
?>
