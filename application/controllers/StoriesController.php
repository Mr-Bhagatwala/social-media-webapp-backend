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

        // getting user Id from sessions
        //$user_id = $this->session->userdata('user_id');

        //     $_FILES is an associative array, and it contains the following keys:
        // $_FILES['file']['name']: The original name of the uploaded file (as it was on the user's computer).
        // $_FILES['file']['type']: The MIME type of the uploaded file (e.g., image/jpeg, application/pdf).
        // $_FILES['file']['tmp_name']: The temporary file path where the uploaded file is stored on the server.
        // $_FILES['file']['error']: The error code (if any) related to the file upload (e.g., 0 means no error).
        // $_FILES['file']['size']: The size of the uploaded file in bytes
                
        //$user_id = $this->session->userdata('user_id');
        $user_id = $this->input->post('user_id');

        // Check if a file is uploaded
        // if (!$this->input->post('media') || empty($_FILES['media']['name'])) {
        //     return $this->output->set_content_type('application/json')
        //                         ->set_output(json_encode(['status' => 'error', 'message' => 'No media file provided', 'file' => $_FILES, "user id id "=> $this->input->post('user_id')]));
        // }x
        if(!$data['media']|| empty($data['user_id'])){
            return $this->output->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'No media file provided', 'file' =>$data['media'], "user id id "=> $data['user_id']]));
        }

        // Configure upload settings
        $config['upload_path']   = './assets/stories/';
        $config['allowed_types'] = 'jpg|jpeg|png|mp4';
        $config['max_size']      = 20480; // Max size in KB (20MB)
        $config['file_name']     = uniqid() . '_' . $_FILES['media']['name'];

        $this->load->library('upload', $config);

        // Try to upload the file
        if (!$this->upload->do_upload('media')) {
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => "Error in uploading stories ". $this->upload->display_errors()]));
        }

        // Get uploaded file data
        $uploadedData = $this->upload->data();
        $mediaPath = 'assets/stories/' . $uploadedData['file_name'];

        // Prepare data for the database
        date_default_timezone_set('Asia/Kolkata');
        $storyData = [
 
            'user_id' => $user_id,
            'media_url' => $mediaPath,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours'))
        ];

        // Save story to the database
        $response = $this->StoriesModel->uploadStory($storyData);

        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode($response));
    }

    // Get all Stories
    public function getStoriesofUser($userId) {
        // Fetch stories from the model
     
        // $stories = $this->StoriesModel->getStories($userId);

        // // Add base URL to media paths
        // foreach ($stories as &$story) {
        //     $story['media_url'] = base_url($story['media_url']);
        // }

        // return $this->output->set_content_type('application/json')
        //                     ->set_output(json_encode($stories));
      
            // Method to get all stories of a user
        
                // Fetch stories from the model
                //get all friend of user


                // $stories = $this->StoriesModel->getStories($userId);
            
                // // Check if stories are returned
                // if (empty($stories)) {
                //     // If no stories found, return an empty array or a custom message
                //     return $this->output->set_content_type('application/json')
                //                         ->set_output(json_encode(["message"=>"no Stories with the specific iserId". $userId])); // Return an empty array
                // }
        
                // // Add base URL to the media paths of each story
                // foreach ($stories as &$story) {
                //     $story['media_url'] = base_url($story['media_url']); // Add the base URL to media URL
                // }
        
                // // Return the stories as JSON response
                // return $this->output->set_content_type('application/json')
                //                     ->set_output(json_encode($stories));
                $friends = $this->FriendRequestModel->getFriendsList($userId);
                $frds_id = [];
                foreach ($friends as $frd) {
                    array_push($frds_id, $frd['friend_id']);
                }
                $res = [];
                foreach ($frds_id as $frd_id) {
                    $res = array_merge($res, $this->StoriesModel->getStories($frd_id));
                }
                // Add base URL to the media paths of each story
                foreach ($res as &$story) {
                    $story['media_url'] = base_url($story['media_url']); // Add the base URL to media URL
                }

                return $this->output->set_content_type('application/json')
                                    ->set_output(json_encode($res));
            
    }

    // Get Stories of the logged in user
    public function getMyStories($userId){

    }
    // Mark Story as Viewed
    public function markStoryAsViewed($storyId) {
        
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

            // $stories =  $this->StoriesModel->getStories($userId);
        
            // foreach ($stories as &$story) {
            //     $story['media_url'] = base_url($story['media_url']); // Add the base URL to media URL

            // }
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
}
?>
