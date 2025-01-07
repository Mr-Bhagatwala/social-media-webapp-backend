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

    // Get visible Stories for a user
    public function getStories($userId) {
        date_default_timezone_set('Asia/Kolkata');
        $this->db->where('user_id', $userId);
        $this->db->where('expires_at >  ', date('Y-m-d H:i:s'));
        //echo date('Y-m-d H:i:s');
        return $this->db->get('stories')->result_array();
    }

    // Mark a story as viewed
    public function markAsViewed($storyId, $viewerId) {
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
}
?>
