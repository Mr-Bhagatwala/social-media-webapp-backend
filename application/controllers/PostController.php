<?php
class PostController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('PostModel');
        $this->load->model('NotificationModel');
        $this->load->model('FriendRequestModel');
        $this->load->helper('url');
        $this->load->library('upload');
        //$this->load->library('session');
        
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


    // Changes required: fetching userId from session
        // $userId = $this->session->userdata('user_id');
    // public function createPost() {



    //     $userId = $this->input->post('user_id');
    //     $content = $this->input->post('content');
    //     $mediaUrls = [];

    //     if (!$userId || ) {
    //         return $this->output->set_content_type('application/json')
    //                             ->set_output(json_encode(['status' => 'error', 'message' => 'User ID is required']));
    //     }

    //     // Handle media upload
    //     if (!empty($_FILES['media'])) {
    //         $files = $_FILES['media'];
           

    //         for ($i = 0; $i < count($_FILES['media']['name']); $i++) {
    //             $_FILES['file']['name']     = $files['name'][$i];
    //             $_FILES['file']['type']     = $files['type'][$i];
    //             $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
    //             $_FILES['file']['error']    = $files['error'][$i];
    //             $_FILES['file']['size']     = $files['size'][$i];

    //             $config['upload_path']   = FCPATH .'./assets/posts/';
    //             $config['allowed_types'] = 'jpg|jpeg|png|mp4|pdf|docx';
    //             $config['max_size']      = 20480; // 20MB
    //             $config['file_name']     = uniqid() . '_' . $_FILES['file']['name'];

    //             $this->upload->initialize($config);

    //             if (!$this->upload->do_upload('file')) {
    //                 return $this->output->set_content_type('application/json')
    //                                     ->set_output(json_encode(['status' => 'error',"count"=>count($_FILES['media']['name']),"fileas are"=>$_FILES['media'],'message' => $this->upload->display_errors()]));
    //             }

    //             $mediaData = $this->upload->data();
    //             $mediaUrls[] = 'assets/posts/' . $mediaData['file_name'];
    //         }
    //     }

    //     // Save post and media
    //     $response = $this->PostModel->createPost($userId, $content, $mediaUrls);

    //     $frd = $this->FriendRequestModel->getFriendsList($userId);
    //     $frdIds = array_column($frd, 'friend_id');
    //     if($response){
    //         $notification = [
    //             'user_ids' => $frdIds,
    //             'message' => "Your friend created a post.",
                
    //         ];
    //         $this->NotificationModel->addNotificationforPost($notification, $userId);

    //         return $this->output->set_content_type('application/json')
    //         ->set_output(json_encode($response));
    //     }
     
    // }
    public function createPost(){
        // Changes required: fetching userId from session
        // $userId = $this->session->userdata('user_id');
        $userId = $this->input->post('user_id'); 
        $content = $this->input->post('content');
        $mediaUrls = [];

        // Validate user ID
        if (empty($userId) || !is_numeric($userId)) {
            return $this->output
                ->set_content_type('application/json') 
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid or missing User ID.']));
        }
    
        // Validate content length
        if (empty($content)) {   
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Content cannot be empty.']));
        }

        // Handle media upload if provided
        if (!empty($_FILES['media']['name'][0])) {
            $files = $_FILES['media'];

            for ($i = 0; $i < count($files['name']); $i++) {
                $_FILES['file']['name']     = $files['name'][$i];
                $_FILES['file']['type']     = $files['type'][$i];
                $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
                $_FILES['file']['error']    = $files['error'][$i];
                $_FILES['file']['size']     = $files['size'][$i];

                $config['upload_path']   = FCPATH . './assets/posts/';
                $config['allowed_types'] = 'jpg|jpeg|png|mp4|pdf|docx';
                $config['max_size']      = 20480; // 20MB
                $config['file_name']     = uniqid() . '_' . $_FILES['file']['name'];

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('file')) {
                    return $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'error',
                            'message' => "Error uploading file: " . $this->upload->display_errors('', '')
                        ]));
                }

                $mediaData = $this->upload->data();
                $mediaUrls[] = 'assets/posts/' . $mediaData['file_name'];
            }
        }

        // Attempt to save the post
        try {
            $response = $this->PostModel->createPost($userId, $content, $mediaUrls);

            if ($response['status'] == "success") {
                $friends = $this->FriendRequestModel->getFriendsList($userId);
                $friendIds = array_column($friends, 'friend_id');
                
                if (!empty($friendIds)) {
                    $notification = [
                        'user_ids' => $friendIds,
                        'message' => "Your friend created a post.",
                    ];
                    $notiRes = $this->NotificationModel->addNotificationforPost($notification, $userId);
                }

                if($notiRes['status'] != "success"){
                    return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'failed', 'message' => 'Post created successfully but Notification not able to send to your friends','friends id lis'=>$friendIds]));
                }
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'success', 'message' => 'Post created successfully and notified to friends']));
            } else {
                throw new Exception("Failed to save the post. Please try again later.");
            }
        } catch (Exception $e) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => $e->getMessage()]));
        }
    }


    // Delete a post
    public function deletePost($postId) {

        // validation post Id 
        if (empty($postId) || !is_numeric($postId)) {
            return $this->output
                ->set_content_type('application/json') 
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid or missing Post Id.']));
        }

        // fetching userId from session: to ensuer that post Id is deleted form only  post author.
        // $userId = $this->session->userdata('user_id');
        // $response = $this->PostModel->deletePost($postId, $userId);
        
        $response = $this->PostModel->deletePost($postId);

        
        if($response){
        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode(["status"=>"success", "message"=>"Post Deleted Succesfully"]));}
        else{
            return $this->output->set_content_type('application/json')
            ->set_output(json_encode(["status"=>"failed", "message"=>"Unable to Delete Post"]));
        }

    }
    

    // Get paginated feed
    // public function getFeed() {
    //     $offset = $this->input->get('offset') ?: 0;
    //     $sort = $this->input->get('sort') ?: 'recent'; // recent/oldest
    //     $user_id = $this->input->get('user_id');
    //     //$userId = $this->session->userdata('user_id');
        
    //     // Validate user ID
    //     if (empty($userId) || !is_numeric($userId)) {
    //         return $this->output
    //             ->set_content_type('application/json') 
    //             ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid or missing User ID.']));
    //     }
        
    //     $response = $this->PostModel->getFeed($offset, $sort);
    
    //     foreach($response as &$res){
          
        
    //         $meds = explode(',', $res['media']);
    //         $newmeds="";
    //         foreach($meds as $med){
    //             if($med){
    //             $med = base_url().$med . ",";
    //             $newmeds  = $newmeds.$med; }
    //         }
            
    //         $res['media'] = rtrim($newmeds, ","); 
  

    //         $res['profile_photo'] = base_url().$res['profile_photo'];
            
    //         $res['likesCount'] = $this->PostModel->getLikesCount($res['post_id']);
    //         $res['commentsCount'] = $this->PostModel->getCommentsCount($res['post_id']);
            
    //         if($user_id){
    //             $islike = $this->PostModel->isLikeByUser($res['post_id'], $user_id);
    //             if($islike){
    //                 $res['islike'] = true;
    //             }else{$res['islike'] = false;}
    //             //$res['islikekaresponse'] = $islike;
                
    //         }else{
    //             $res['islike'] = false;
    //         }
    //     }
        
    //     return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'feed fetched Succesfully', 'data'=> $response]));
    // }


    // public function getFeed(){
    //     $offset = $this->input->get('offset') ?: 0; // Use XSS filtering
    //     $sort = $this->input->get('sort') ?: 'recent'; // 'recent' or 'oldest'
    //     $user_id = $this->input->get('user_id');
    //      // Changes required: fetching userId from session
    //     // $userId = $this->session->userdata('user_id');


    //     // Validate inputs
    //     if (!is_numeric($offset) || $offset < 0) {
    //         return $this->output
    //             ->set_content_type('application/json')
    //             ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid offset value.']));
    //     }

    //     if (!in_array($sort, ['recent', 'oldest'])) {
    //         return $this->output
    //             ->set_content_type('application/json')
    //             ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid sort parameter.']));
    //     }

    //     if (!empty($user_id) && !is_numeric($user_id)) {
    //         return $this->output
    //             ->set_content_type('application/json')
    //             ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid User ID.']));
    //     }

    //     // Fetch feed from the model
    //     $response = $this->PostModel->getFeed($offset, $sort);
       
    //     if(!$response){
    //         return $this->output
    //         ->set_content_type('application/json')
    //         ->set_output(json_encode(['status' => 'success', 'message' => 'No Feed to display']));
    //     }
    //     // Process each feed item


    //     foreach ($response as &$res) {
    //         $mediaList = explode(',', $res['media']);
    //         $processedMedia = [];

    //         foreach ($mediaList as $media) {
    //             if (!empty($media)) {
    //                 $processedMedia[] = base_url() . $media;
    //             }
    //         }

    //         $res['media'] = $processedMedia; // Now an array of URLs
    //         $res['profile_photo'] = !empty($res['profile_photo']) ? base_url() . $res['profile_photo'] : null;

    //         // Add likes and comments counts
    //         //$res['likesCount'] = $this->PostModel->getLikesCount($res['post_id']);
    //         //$res['commentsCount'] = $this->PostModel->getCommentsCount($res['post_id']);

    //         // Check if the user has liked the post
    //         if($user_id){
    //             $islike = $this->PostModel->isLikeByUser($res['post_id'], $user_id);
    //             if($islike){
    //                 $res['islike'] = true;
    //             }else{$res['islike'] = false;}
    //             //$res['islikekaresponse'] = $islike;
                
    //         }else{
    //             $res['islike'] = false;
    //         }
    //     }

    //     return $this->output
    //         ->set_content_type('application/json')
    //         ->set_output(json_encode(['status' => 'success', 'message' => 'Feed fetched successfully.', 'data' => $response]));
    // }
    public function getFeed() {
        $offset = $this->input->get('offset') ?: 0; // Use XSS filtering
        $sort = $this->input->get('sort') ?: 'recent'; // 'recent' or 'oldest'
        $user_id = $this->input->get('user_id');
        
        // Validate inputs
        if (!is_numeric($offset) || $offset < 0) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid offset value.']));
        }
    
        if (!in_array($sort, ['recent', 'oldest'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid sort parameter.']));
        }
    
        if (!empty($user_id) && !is_numeric($user_id)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid User ID.']));
        }
    
        // Fetch feed from the model
        $response = $this->PostModel->getFeed($offset, $sort);
        
        if(!$response){
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'No Feed to display']));
        }
    
        // Initialize an empty array to store liked post IDs
        $likedPostIds = [];
    
        // Check if user is logged in and fetch all the posts liked by the user
        if ($user_id) {
            $likedPostIds = $this->PostModel->getLikedPostsByUser($user_id);
        }
    
        // Process each feed item
        foreach ($response as &$res) {
            $mediaList = explode(',', $res['media']);
            $processedMedia = [];
    
            foreach ($mediaList as $media) {
                if (!empty($media)) {
                    $processedMedia[] = base_url() . $media;
                }
            }
    
            $res['media'] = $processedMedia; // Now an array of URLs
            $res['profile_photo'] = !empty($res['profile_photo']) ? base_url() . $res['profile_photo'] : null;
    
            // Add likes and comments counts
            //$res['likesCount'] = $this->PostModel->getLikesCount($res['post_id']);
            //$res['commentsCount'] = $this->PostModel->getCommentsCount($res['post_id']);
    
            // Check if the user has liked the post (check outside the loop)
            if ($user_id) {
                // Set 'islike' based on the list of liked post IDs
                $res['islike'] = in_array($res['post_id'], $likedPostIds);
            } else {
                $res['islike'] = false;
            }
        }
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Feed fetched successfully.', 'data' => $response]));
    }
    

 
    // Like a post
    // public function likePost($postId) {
    //     // take useriD from session
    //      //$userId = $this->session->userdata('user_id');
    //    // $userId = $this->input->post('user_id');
    //    $data = json_decode(file_get_contents('php://input'), true);

    //    $userId = $data['user_id'];
   
    //     $response = $this->PostModel->likePost($postId, $userId);

    //     return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    // }

    // // Add a comment
    // public function addComment($postId) {

    //      // getting user Id from session
    //     //$user_id = $this->session->userdata('user_id');
    //     $data = json_decode(file_get_contents('php://input'), true);

    //     $user_id = $data['user_id'];
    //     $author_id = $data['author_id'];
    //     $commentData = [
    //         'post_id' => $postId,
    //         'user_id' => $user_id,
    //       //  'parent_comment_id' => $this->input->post('parent_comment_id'),
    //         'content' => $data['content'],
    //     ];
    //     $parent_comment = $data['parent_comment_id'];
    //     if( $parent_comment){
    //         $commentData['parent_comment_id'] =  $parent_comment;
    //     }
    //     $response = $this->PostModel->addComment($commentData);

    //     $notification=[
    //         "message"=> "User commented on your post",
    //         "source_id"=>$user_id,
    //         "user_id"=>$author_id
    //     ];
    //     $this->NotificationModel->addNotification($notification);
    //     return $this->output->set_content_type('application/json')
    //                         ->set_output(json_encode(["data"=>$response, "status"=>"success"]));
    // }


    // public function getComments($postId){
    //     $response = $this->PostModel->getCommentsofPost($postId);
    //     //in every response i am sending profile_photo i want to update it by baseUrl().profile_photo
    //     foreach($response as $key => $value){
    //         $response[$key]['profile_photo'] = base_url().$response[$key]['profile_photo'];
    //     }

    //     return $this->output->set_content_type('application/json')
    //                         ->set_output(json_encode($response));
    // }
    // Like a post
    public function likePost($postId){
        // Decode JSON input
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($postId) || !is_numeric($postId)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid or missing Post ID.']));
        }

        if (empty($data['user_id']) || !is_numeric($data['user_id'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid or missing User ID.']));
        }

        $response = $this->PostModel->likePost($postId, $data['user_id']);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Post liked successfully.', 'data' => $response]));
    }

    // Add a comment
    public function addComment($postId){
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($postId) || !is_numeric($postId)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid or missing Post ID.']));
        }

        if (empty($data['user_id']) || !is_numeric($data['user_id'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid or missing User ID.']));
        }

        if (empty($data['content'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Comment content cannot be empty.']));
        }

        $commentData = [
            'post_id' => $postId,
            'user_id' => $data['user_id'],
            'content' => $data['content'],
        ];

        if (!empty($data['parent_comment_id']) && is_numeric($data['parent_comment_id'])) {
            $commentData['parent_comment_id'] = $data['parent_comment_id'];
        }

        $response = $this->PostModel->addComment($commentData);

        if (!empty($data['author_id'])) {
            $notification = [
                'message' => 'User commented on your post',
                'source_id' => $data['user_id'],
                'user_id' => $data['author_id'],
            ];
            $this->NotificationModel->addNotification($notification);
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Comment added successfully.', 'data' => $response]));
    }

    // Get comments for a post
    public function getComments($postId){
        if (empty($postId) || !is_numeric($postId)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid or missing Post ID.']));
        }

        $response = $this->PostModel->getCommentsofPost($postId);

        foreach ($response as $key => $value) {
            $response[$key]['profile_photo'] = !empty($value['profile_photo']) 
                ? base_url() . $value['profile_photo'] 
                : null;
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Comments fetched successfully.', 'data' => $response]));
    }

    public function getUserPost(){
        $uId = $this->input->get("uId", true);
        
        $user_id = $this->input->get("user_id", true);
        // Changes required: fetching userId from session
        // $userId = $this->session->userdata('user_id');


        if (empty($uId) || !is_numeric($uId)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid or missing Id of profile.']));
        }
        
        if (empty($user_id) || !is_numeric($user_id)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Invalid or missing login userId']));
        }
        $response = $this->PostModel->getUserPost($uId);
    
        
        if ($user_id) {
            $likedPostIds = $this->PostModel->getLikedPostsByUser($user_id);
        }

        // foreach($response as &$res){
        //     //  $meds = $res['media'].split(',');
          
        //       $meds = explode(',', $res['media']);
        //       $newmeds="";
        //       foreach($meds as $med){
        //           if($med){
        //           $med = base_url().$med . ",";
        //           $newmeds  = $newmeds.$med; }
        //       }

        //       $res['media'] = rtrim($newmeds, ","); 
  
        //       $res['profile_photo'] = base_url().$res['profile_photo']; 
              
        //     $res['likesCount'] = $this->PostModel->getLikesCount($res['post_id']);
        //     $res['commentsCount'] = $this->PostModel->getCommentsCount($res['post_id']);
            
        //     if($user_id){
        //         $islike = $this->PostModel->isLikeByUser($res['post_id'], $user_id);
        //         if($islike){
        //             $res['islike'] = true;
        //         }else{$res['islike'] = false;}
        //         //$res['islikekaresponse'] = $islike;
                
        //     }else{
        //         $res['islike'] = false;
        //     }
        // }
        foreach ($response as &$res) {
            $meds = explode(',', $res['media']);
            $newmeds = "";
        
            foreach ($meds as $med) {
                if (!empty($med)) {
                    $med = base_url() . $med . ",";
                    $newmeds .= $med;
                }
            }
        
            $res['media'] = rtrim($newmeds, ","); // Concatenate the URLs into a single string
        
            $res['profile_photo'] = !empty($res['profile_photo']) 
                ? base_url() . $res['profile_photo'] 
                : null;
        
            //$res['likesCount'] = $this->PostModel->getLikesCount($res['post_id']);
            //$res['commentsCount'] = $this->PostModel->getCommentsCount($res['post_id']);
        
            if ($user_id) {
                // Set 'islike' based on the list of liked post IDs
                $res['islike'] = in_array($res['post_id'], $likedPostIds);
            } else {
                $res['islike'] = false;
            }
        }
        
        return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'success', 'message' => 'User posts fetched successfully.', 'data' => $response]));
    }


    // public function toggle_like() {
    //     // Get post_id and user_id from the POST request
    //     $data = json_decode(file_get_contents('php://input'), true);

    //     $post_id = $data['post_id'];
    //     $user_id = $data['user_id']; // Assuming user_id is stored in session
        
    //     // echo "Post id hai : ".$post_id;
    //     // echo "User id hai : " . $user_id;

    //     $like = $this->PostModel->isLikeByUser($post_id, $user_id);
    
    //     if ($like) {
    //         $res = $this->PostModel->removeLike($post_id, $user_id);
    //         $action = $res['message'];
    //     } else {
    //         // User has not liked, add the like
    //         $res = $this->PostModel->likePost($post_id, $user_id);
    //         $action = $res['message'];
    //     }
    
    //     // Return the number of likes for this post after the action
    //     $likes_count = $this->PostModel->getLikesCount($post_id);
        
    //     // Return the result in JSON format
    //     echo json_encode([
    //         'status' => 'success',
    //         'action' => $action,
    //         'likes_count' => $likes_count
    //     ]);
    // }
    
    public function toggle_like() {
        // Decode JSON input
        $data = json_decode(file_get_contents('php://input'), true);
    
        // Validate post_id
        if (empty($data['post_id']) || !is_numeric($data['post_id'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid or missing post ID']));
        }
    
        // Validate user_id
        if (empty($data['user_id']) || !is_numeric($data['user_id'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid or missing user ID']));
        }
    
        $post_id = $data['post_id'];
        $user_id = $data['user_id'];
        
        // Changes required: fetching userId from session
        // $userId = $this->session->userdata('user_id');

        // Check if the user has already liked the post
        $like = $this->PostModel->isLikeByUser($post_id, $user_id);
    
        if ($like) {
            // Remove the like if it already exists
            $res = $this->PostModel->removeLike($post_id, $user_id);
            
            $action = $res['status'] == "success" ? "removed":"added";
        } else {
            // Add the like if it doesn't exist
            $res = $this->PostModel->likePost($post_id, $user_id);
          
            $action = $res['status'] == "success" ? "added":"removed";
        }
    
        // Fetch updated like count
        $likes_count = $this->PostModel->getLikesCount($post_id);
    
        // Return response in JSON format
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'action' => $action,
                'likes_count' => $likes_count
            ]));
    }
    
}

?>






