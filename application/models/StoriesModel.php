<?php
class StoriesModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Upload a Story
    public function uploadStory($data) {

        $this->db->insert('stories', $data);
        if ($this->db->affected_rows() > 0) {
            return ['status' => 'success', 'story_id' => $this->db->insert_id()];
        }
        return ['status' => 'error', 'message' => 'Failed to upload story'];
    }
    public function getStories($userId) {
        if($userId == null){
            return null;
        }
        date_default_timezone_set('Asia/Kolkata');
    
        // Select fields from both stories and users
        $this->db->select('
            stories.*, 
            users.name AS name, 
            users.profile_photo AS profile_photo
        ');
    
        // Join with the users table to get the name and profile photo
        $this->db->from('stories');
        $this->db->join('users', 'users.id = stories.user_id', 'inner'); // Inner join to get user details
    
        // Apply the conditions
        $this->db->where('stories.user_id', $userId);
        $this->db->where('stories.expires_at >', date('Y-m-d H:i:s')); // Only active stories
    
        // Get the results and return as an array
        $result = $this->db->get()->result_array();
    
        return $result;
    }
    

    // Mark a story as viewed
    public function markAsViewed($storyId, $viewerId) {
        if($storyId == null || $viewerId == null){
            return null;
        }
        date_default_timezone_set('Asia/Kolkata');
        $data = [
            'story_id' => $storyId,
            'viewer_id' => $viewerId,
            'viewed_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('story_views', $data);
        return $this->db->affected_rows() > 0;
    }

    // Add or update reaction to a Story
    public function reactToStory($data) {
        $this->db->where('story_id', $data['story_id']);
        $this->db->where('user_id', $data['user_id']);
        $existingReaction = $this->db->get('story_reactions')->row_array();

        if ($existingReaction) {
            $this->db->where('story_id', $existingReaction['story_id']);
            return $this->db->update('story_reactions', $data);
        } else {
            return $this->db->insert('story_reactions', $data);
        }
    }

    // Delete expired stories
    public function deleteExpiredStories() {
        $this->db->where('expires_at <', date('Y-m-d H:i:s'));
        $this->db->delete('stories');
        return $this->db->affected_rows();
    }

    public function isViewedByUser($storyId, $viewerId) {
        if($storyId == null || $viewerId == null){
            return null;
        }
        $this->db->where('story_id', $storyId);
        $this->db->where('viewer_id', $viewerId);
        return $this->db->get('story_views')->result_array();
    }

    //get number of views for a story
    public function getStoryViews($storyId) {
        if($storyId == null){
            return null;
        }
        $this->db->where('story_id', $storyId);
        return $this->db->get('story_views')->num_rows();
    }

    public function like($storyId, $userId) {
        if($storyId == null || $userId == null){
            return null;
        }
        $data = [
            'story_id' => $storyId,
            'user_id' => $userId,
        ];
        $this->db->insert('likes', $data);
        return $this->db->affected_rows() > 0;
    }
    public function getLikes($storyId) {
        if($storyId == null){
            return null;
        }
        $this->db->where('story_id', $storyId);
        return $this->db->get('likes')->num_rows();
        }
    public function isLiked($storyId, $userId) {
        if($storyId == null || $userId == null){
            return null;
        }
        $this->db->where('story_id', $storyId);
        $this->db->where('user_id', $userId);
        return $this->db->get('likes')->result_array();
    }
}
?>
