<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');  // Allow all origins
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');  // Allow these methods
        header('Access-Control-Allow-Headers: Content-Typ   e, Authorization');
class StoriesController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('StoriesModel');
        $this->load->model('NotificationModel');
        $this->load->model('FriendRequestModel');
        $this->load->helper('url'); 
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(200);  // Respond with HTTP OK status
exit; // Terminate the script after the preflight response
        }

        // CORS headers for other requests
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization'); 
    }

    // Upload a Story
    // public function uploadStory() {
    //     $data = $this->input->post();

    //     // Validate input
    //     if (empty($data['user_id']) || empty($data['media_url']) || empty($data['expires_at'])) {
    //         return $this->output->set_content_type('application/json')
    //                             ->set_output(json_encode(['status' => 'error', 'message' => 'Missing required fields']));
    //     }

    //     // Call model to upload the story
    //     $response = $this->StoriesModel->uploadStory($data);
    //     return $this->output->set_content_type('application/json')
    //                         ->set_output(json_encode($response));
    // }

    // // Get Stories of a User
    // public function getStories($userId) {
    //     // Validate input
    //     if (empty($userId)) {
    //         return $this->output->set_content_type('application/json')
    //                             ->set_output(json_encode(['status' => 'error', 'message' => 'User ID is required']));
    //     }

    //     // Fetch stories
    //     $response = $this->StoriesModel->getStories($userId);
    //     return $this->output->set_content_type('application/json')
    //                         ->set_output(json_encode($response));
    // }

    // Upload a Story
    public function uploadStory() {
        $config['upload_path'] = FCPATH .'assets/stories/';
        $config['allowed_types'] = 'video/mp4|jpg|jpeg|png|gif|webp|mp4|webm|ogg|mkv|avi|mov|video/mp4';
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('media')) {
        $error = $this->upload->display_errors();
        echo json_encode(['status' => 'error', 'messages' => $error]);
        } else {
        $data = $this->upload->data();
        $userId = $this->input->post('userId');
        if($userId == null || !is_numeric($userId)){
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'User ID is required']));
        }
        //add user story to story model
        $storyData = [
            'user_id' => $userId,
            'media_url' => 'assets/stories/' . $data['file_name'],
            'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours'))
        ];
        $res = $this->StoriesModel->uploadStory($storyData);
        //if success if error
        if($res['status'] == "error"){
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload story']);
        }else{
            echo json_encode(['status' => 'success', 'file_name' => $data['file_name']]);
        }   
        }
    }

    // Get all Stories
    public function getStoriesOfUser($userId) {
        // Validate input
        if (empty($userId) || !is_numeric($userId)) {
            return $this->output->set_content_type('application/json')
                            ->set_output(json_encode(['status' => 'error', 'message' => 'User ID is required']));
        }
        // Get the list of friends
        $friends = $this->FriendRequestModel->getFriendsList($userId);
        $frds_id = [];
        foreach ($friends as $frd) {
            array_push($frds_id, $frd['friend_id']);
        }
    
        // Fetch stories from friends
        $res = [];
        foreach ($frds_id as $frd_id) {
            $res = array_merge($res, $this->StoriesModel->getStories($frd_id));
        }
    
        // Fetch the user's own stories
        $userStories = $this->StoriesModel->getStories($userId);
    
        // Merge user's stories with friends' stories
        $res = array_merge($userStories, $res);
    
        // Add base URL to the media paths of each story
        foreach ($res as &$story) {
            $story['media_url'] = base_url().($story['media_url']); // Add the base URL to media URL
        }
        foreach ($res as &$story) {
            if(strlen($story['profile_photo'])!=0) {
                $story['profile_photo'] = base_url().($story['profile_photo']); // Add the base URL to media URL
            }
        }
    
        // Return the response as JSON
        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode($res));
    }    

    // Get Stories of the logged in user
    public function getMyStories($userId){
        // Fetch stories from the model
        if($userId == null || !is_numeric($userId)){
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'User ID is required']));
        }
        $stories = $this->StoriesModel->getStories($userId);
        
        // Add base URL to media paths
        foreach ($stories as &$story) {
            $story['media_url'] = base_url($story['media_url']);
        }
        
        // Return the stories as JSON response
        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode($stories));

    }
    // Mark Story as Viewed
    public function markStoryAsViewed($storyId) {
        // Check if story ID is valid
        if (!is_numeric($storyId)) {
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid story ID']));
        }
        // getting user Id from cookies
        //$viewerId = $this->session->userdata('user_id');
        $data = json_decode(file_get_contents('php://input'), true);
        //   $viewerId = $this->input->post('viewer_id');
        $viewerId =  $data['userId'];
        // Validate input
        if (empty($storyId) || empty($viewerId)) {
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Story ID and Viewer ID are required']))
                                ->set_output(json_encode($viewerId));
        }

        // Mark story as viewed
        $response = $this->StoriesModel->markAsViewed($storyId, $viewerId);
        if ($response == false) {
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to mark story as viewed']));
        }
        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode($response));
    }

    // React to Story
    public function reactToStory($storyId) {
        // GETTING userid and reaction form the frontend
        $reactionData = json_decode(file_get_contents('php://input'), true);
        
        // getting user Id from cookies
        //$user_id = $this->session->userdata('user_id');
        //reactionData['user_id'] = $user_id;;
        
        // Validate input
       // echo "reaction is ".$reactionData;

        if (empty($reactionData['user_id']) || empty($reactionData['reaction']) || empty($storyId)) {
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Missing required fields']));
        }

        $reactionData['story_id'] = $storyId;

        // Record reaction
        $response = $this->StoriesModel->reactToStory($reactionData);
        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode($response));
    }

    public function getFriendsStories($userId){
        if (!is_numeric($userId)) {
            return $this->output->set_status_header(400)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid user ID.',"data is "=>$data2]));
        }

        $friends = $this->FriendRequestModel->getFriendsList($userId);
        $frds_id = [];

        foreach($friends as $frd){
            array_push($frds_id, $frd["friend_id"]);
        }
        
        $res=[];
        foreach($frds_id as $ids){
            $stories = $this->StoriesModel->getStories($ids);
         
            // Add base URL to the media paths of each story
            foreach ($stories as &$story) {
                $story['media_url'] = base_url($story['media_url']); // Add the base URL to media URL
                //echo "id is ".$ids . "story['emdia'] ". $story['media_url'];
            }
            $res[$ids] = $stories;
        }
        return $this->output->set_status_header(200)
                            ->set_content_type('application/json')
                            ->set_output(json_encode(['status' => 'success', 'data' => $res]));

        
    }

    // Delete Expired Stories
    public function deleteExpiredStories() {
        // Delete expired stories
        $response = $this->StoriesModel->deleteExpiredStories();
        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode($response));
    }

    public function isViewedByUser($storyId){
        if($storyId == null){
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Story ID is required']));
        }
        $viewerId = $this->input->get('userId');
        $response = $this->StoriesModel->isViewedByUser($storyId, $viewerId);
        $res = sizeof($response)>0;
        return $this->output->set_content_type('application/json')
                        ->set_output(json_encode($res));
    }
    //get story views
    public function getStoryView($storyId){
        if($storyId == null || !is_numeric($storyId)){
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Story ID is required']));
        }
        $response = $this->StoriesModel->getStoryViews($storyId);
        return $this->output->set_content_type('application/json')
                        ->set_output(json_encode($response));
    }
    public function like($storyId){
        if($storyId == null || !is_numeric($storyId)){
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Story ID is required']));
        }
       $userId = $this->input->get('userId');
       if ($userId == null || !is_numeric($userId)) {
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'User ID is required']));
        }
        $response = $this->StoriesModel->like($storyId, $userId);
        return $this->output->set_content_type('application/json')
                        ->set_output(json_encode($response));
    }
    //get all likes of a story
    public function getLikes($storyId){
        if($storyId == null || !is_numeric($storyId)){
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Story ID is required']));
        }
        $response = $this->StoriesModel->getLikes($storyId);
        
        return $this->output->set_content_type('application/json')
                        ->set_output(json_encode($response));
    }

    public function isLiked($storyId){
        if($storyId == null || !is_numeric($storyId)){
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Story ID is required']));
        }
        $userId = $this->input->get('userId');
        if ($userId == null || !is_numeric($userId)) {
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'User ID is required']));
        }
        $response = $this->StoriesModel->isLiked($storyId, $userId);
        return $this->output->set_content_type('application/json')
                        ->set_output(json_encode($response));
    }
}
?>
