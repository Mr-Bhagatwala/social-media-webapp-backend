<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// header('Access-Control-Allow-Origin: *');  // Allow all origins
//         header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');  // Allow these methods
//         header('Access-Control-Allow-Headers: Content-Type, Authorization');


class PostController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('PostModel');
        $this->load->model('NotificationModel');
        $this->load->model('FriendRequestModel');
        $this->load->helper('url');
        $this->load->library('upload');

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(200);  // Respond with HTTP OK status
            exit;  // Terminate the script after the preflight response
        }

        // CORS headers for other requests
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    // Create a new post

    // In case of multiple mb_encoding_aliases
    //$_FILES['userfiles'] = array(
    //     'name'     => array('file1.jpg', 'file2.mp4'),   // Array of original file names
    //     'type'     => array('image/jpeg', 'video/mp4'),   // Array of MIME types
    //     'tmp_name' => array('/tmp/phpYzdqk1', '/tmp/phpYzdqk2'),  // Array of temporary file paths
    //     'error'    => array(0, 0),                        // Array of error codes (0 means no error)
    //     'size'     => array(123456, 789012),              // Array of file sizes in bytes
    // );
    
    public function createPost() {

        // getting user Id from session
        //$user_id = $this->session->userdata('user_id');

        $userId = $this->input->post('user_id');
        $content = $this->input->post('content');
        $mediaUrls = [];

        if (!$userId) {
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'User ID is required']));
        }

        // Handle media upload
        if (!empty($_FILES['media'])) {
            $files = $_FILES['media'];
           

            for ($i = 0; $i < count($_FILES['media']['name']); $i++) {
                $_FILES['file']['name']     = $files['name'][$i];
                $_FILES['file']['type']     = $files['type'][$i];
                $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
                $_FILES['file']['error']    = $files['error'][$i];
                $_FILES['file']['size']     = $files['size'][$i];

                $config['upload_path']   = FCPATH .'./assets/posts/';
                $config['allowed_types'] = 'jpg|jpeg|png|mp4|pdf|docx';
                $config['max_size']      = 20480; // 20MB
                $config['file_name']     = uniqid() . '_' . $_FILES['file']['name'];

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('file')) {
                    return $this->output->set_content_type('application/json')
                                        ->set_output(json_encode(['status' => 'error',"count"=>count($_FILES['media']['name']),"fileas are"=>$_FILES['media'],'message' => $this->upload->display_errors()]));
                }

                $mediaData = $this->upload->data();
                $mediaUrls[] = 'assets/posts/' . $mediaData['file_name'];
            }
        }

        // Save post and media
        $response = $this->PostModel->createPost($userId, $content, $mediaUrls);

        $frd = $this->FriendRequestModel->getFriendsList($userId);
        $frdIds = array_column($frd, 'friend_id');
        if($response){
            $notification = [
                'user_ids' => $frdIds,
                'message' => "Your friend created a post.",
             
            ];
            $this->NotificationModel->addNotificationforPost($notification);

            return $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
        }
     
    }

    // Delete a post
    public function deletePost($postId) {

        $response = $this->PostModel->deletePost($postId, $userId);

        if($response){
        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode(["status"=>"success", "message"=>"Post deleted succesfully"]));}
        else{
            return $this->output->set_content_type('application/json')
            ->set_output(json_encode(["status"=>"failed", "message"=>"Post deleted not succesfully"]));
        }

    }
    

    // Get paginated feed
    public function getFeed() {
        $offset = $this->input->get('offset') ?: 0;
        $sort = $this->input->get('sort') ?: 'recent'; // recent/oldest

        $user_id = $this->input->get('user_id');
        $response = $this->PostModel->getFeed($offset, $sort);
    
        // $data = json_decode(file_get_contents('php://input'), true);

        // $user_id = $data['user_id'];
    
        foreach($response as &$res){
          //  $meds = $res['media'].split(',');
        
            $meds = explode(',', $res['media']);
            $newmeds="";
            foreach($meds as $med){
                if($med){
                $med = base_url().$med . ",";
                $newmeds  = $newmeds.$med; }
            }
            
            $res['media'] = rtrim($newmeds, ","); 
  
  //          $res['media'] = $newmeds ;
            $res['profile_photo'] = base_url().$res['profile_photo'];
            
            $res['likesCount'] = $this->PostModel->getLikesCount($res['post_id']);
            $res['commentsCount'] = $this->PostModel->getCommentsCount($res['post_id']);
            
            if($user_id){
                $islike = $this->PostModel->isLikeByUser($res['post_id'], $user_id);
                if($islike){
                    $res['islike'] = true;
                }else{$res['islike'] = false;}
                //$res['islikekaresponse'] = $islike;
                
            }else{
                $res['islike'] = false;
            }
        }
        
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

 
    // Like a post
    public function likePost($postId) {
        // take useriD from session
         //$userId = $this->session->userdata('user_id');
       // $userId = $this->input->post('user_id');
       $data = json_decode(file_get_contents('php://input'), true);

       $userId = $data['user_id'];
   
        $response = $this->PostModel->likePost($postId, $userId);

        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    // public function countlikesofPost($postId){

    // }

    // Add a comment
    public function addComment($postId) {

         // getting user Id from session
        //$user_id = $this->session->userdata('user_id');
        $data = json_decode(file_get_contents('php://input'), true);

        $user_id = $data['user_id'];
    
        $commentData = [
            'post_id' => $postId,
            'user_id' => $user_id,
          //  'parent_comment_id' => $this->input->post('parent_comment_id'),
            'content' => $data['content'],
        ];
        $parent_comment = $data['parent_comment_id'];
        if( $parent_comment){
            $commentData['parent_comment_id'] =  $parent_comment;
        }
        $response = $this->PostModel->addComment($commentData);

        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode($response));
    }

    // get comments of a postid
    // public function getComments($postId){
    //     $response = $this->PostModel->getCommentsofPost($postId);
    //     return $this->output->set_content_type('application/json')
    //                         ->set_output(json_encode($response));
    // }

    public function getComments($postId){
        $response = $this->PostModel->getCommentsofPost($postId);
        //in every response i am sending profile_photo i want to update it by baseUrl().profile_photo
        foreach($response as $key => $value){
            $response[$key]['profile_photo'] = base_url().$response[$key]['profile_photo'];
        }

        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode($response));
    }
    // // Get notifications
    // public function getNotifications() {
    //     $userId = $this->input->get('user_id');
    //     $response = $this->PostsModel->getNotifications($userId);

    //     return $this->output->set_content_type('application/json')
    //                         ->set_output(json_encode($response));
    // }
     public function getUserPost(){

        $uId = $this->input->get("uId");
    
        $user_id = $this->input->get("user_id");
        // echo "uid is  ". $uId;
        // echo "user id is ".$user_id;
        $response = $this->PostModel->getUserPost($uId);
        
        
        foreach($response as &$res){
            //  $meds = $res['media'].split(',');
          
              $meds = explode(',', $res['media']);
              $newmeds="";
              foreach($meds as $med){
                  if($med){
                  $med = base_url().$med . ",";
                  $newmeds  = $newmeds.$med; }
              }

              $res['media'] = rtrim($newmeds, ","); 
  
              $res['profile_photo'] = base_url().$res['profile_photo']; 
              
            $res['likesCount'] = $this->PostModel->getLikesCount($res['post_id']);
            $res['commentsCount'] = $this->PostModel->getCommentsCount($res['post_id']);
            
            if($user_id){
                $islike = $this->PostModel->isLikeByUser($res['post_id'], $user_id);
                if($islike){
                    $res['islike'] = true;
                }else{$res['islike'] = false;}
                //$res['islikekaresponse'] = $islike;
                
            }else{
                $res['islike'] = false;
            }
          }
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }


    public function toggle_like() {
        // Get post_id and user_id from the POST request
        $data = json_decode(file_get_contents('php://input'), true);

        $post_id = $data['post_id'];
        $user_id = $data['user_id']; // Assuming user_id is stored in session
        
        // echo "Post id hai : ".$post_id;
        // echo "User id hai : " . $user_id;

        $like = $this->PostModel->isLikeByUser($post_id, $user_id);
    
        if ($like) {
            $res = $this->PostModel->removeLike($post_id, $user_id);
            $action = $res['message'];
        } else {
            // User has not liked, add the like
            $res = $this->PostModel->likePost($post_id, $user_id);
            $action = $res['message'];
        }
    
        // Return the number of likes for this post after the action
        $likes_count = $this->PostModel->getLikesCount($post_id);
        
        // Return the result in JSON format
        echo json_encode([
            'status' => 'success',
            'action' => $action,
            'likes_count' => $likes_count
        ]);
    }
    
}
?>
