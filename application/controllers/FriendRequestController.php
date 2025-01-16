
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// header('Access-Control-Allow-Origin: *');  // Allow all origins
// header('Access-Control-Allow-Methods: POST, GET, OPTIONS');  // Allow these methods
//         // header('Access-Control-Allow-Headers: Content-Type:, Authorization');
//     header('Access-Control-Allow-Headers:Origin,Accept,Content-Type, Authorization, X-Request-With');
// header('Access-Control-Allow-Origin: *');  // Allow all origins
//         header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');  // Allow these methods
//         header('Access-Control-Allow-Headers: Content-Type, Authorization');
class FriendRequestController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('FriendRequestModel');
        $this->load->model('NotificationModel');
        $this->load->library('form_validation'); // For input validation 
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

    /**
     * Send a Friend Request
     */
    // fields required: sender_id, receiver_id
    // send data i form-data
    //new one send as json
    public function sendRequest() {

        // $data = $this->input->post(); // form-data
       $data = json_decode(file_get_contents('php://input'), true); // Get raw JSON data
        //$request_id = $data['receiver_id']; // Access the 'receiver_id' value from the associative array
        //$sender_id = $data['sender_id']; // Access the 'sender_id' value from the associative array
        // 

        $sender_id = $data['sender_id'];
        $receiver_id = $data['receiver_id'];
        
        // optimisation
        //$sender_id = $this->session->userdata('user_data');
        // $receiver_id  from sendRequest($receiver_id);

        // Validate input
        //$this->form_validation->set_data($data);
        $_POST=$data;   
        // $this->form_validation->set_rules('sender_id', 'Sender ID', 'required|numeric');
        // $this->form_validation->set_rules('receiver_id', 'Receiver ID', 'required|numeric');

        // if ($this->form_validation->run() == FALSE) {
        //     return $this->output->set_status_header(400)
        //                         ->set_content_type('application/json')
        //                         ->set_output(json_encode(['status' => 'error','data'=>$data, 'message' => 'There has been an error in the input: ' . validation_errors()]));
        // }

        // Check if a request already exists
        if ($this->FriendRequestModel->checkExistingRequest($sender_id, $receiver_id)) {
            return $this->output->set_status_header(409)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Friend request already exists.']));
        }

        if ($this->FriendRequestModel->checkExistingRequest($receiver_id, $sender_id)) {
            return $this->output->set_status_header(409)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Friend request already exists.']));
        }

        // Send friend request
        $response = $this->FriendRequestModel->sendRequest($data);

        if ($response) {
            // Add Notification
            $notification = [
                'user_id' => $receiver_id,
                'message' => "You have a new friend request.",
             
            ];
            $this->NotificationModel->addNotification($notification);

            return $this->output->set_status_header(200)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'success', 'message' => 'Friend request sent successfully.']));
        } else {
            return $this->output->set_status_header(500)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to send friend request.']));
        }
    }


    /**
     * Get all Friend Requests for a user
     */ 
    // params required: receiver_id  (current userid), status(optional) bydefault: pending

    // send data in query params
    // loxl/:di
    public function getRequests($userId) {
        $type = $this->input->get('type') ?: 'pending';
        
        //        $data = $this->input->get() || json_decode(file_get_contents('php://input'), true);
        if ($this->input->get()) {
            // If query parameters exist, use them
            $data = $this->input->get();
        } else {
            // If no query parameters, check the body (JSON).
            $data = json_decode(file_get_contents('php://input'), true);
        }
        // $userId = $data['user_id'];
        if (!$userId) {

            return $this->output->set_status_header(400)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid user ID.','data'=>$data]));
        }

        $requests = $this->FriendRequestModel->getRequests($userId, $type);

        return $this->output->set_status_header(200)
                            ->set_content_type('application/json')
                            ->set_output(json_encode(['status' => 'success', 'data' => $requests,'user id '=> $userId]));
    }

    /**
     * Respond to Friend Request
     */

    public function respondRequest() {
        //decode in json
        $data = json_decode(file_get_contents('php://input'), true);

        $status = $data['status']; // status
        $sender_id = $data['sender_id']; // request_id source id
        $receiver_id = $data['receiver_id']; // user_id 
        //$user_id = $this->session->userdata('user_data');
        // var_dump($status,$request_id,$user_id);


        if (!in_array($status, ['accepted', 'rejected'])) {
            return $this->output->set_status_header(400)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid status value.']));
        }

        $response = $this->FriendRequestModel->respondRequest($sender_id, $receiver_id, $status);

        if ($response) {
            if ($status === 'accepted') {
                $notification = [
                    'user_id' => $receiver_id,
                    'source_id' => $sender_id,
                    'message' => "You are now friends ",   
                ];
                $this->NotificationModel->addNotification($notification);

                $notification = [
                    'user_id' => $sender_id,
                    'source_id' => $receiver_id,
                    'message' => "You are now friends ",   
                ];
                $this->NotificationModel->addNotification($notification);
              //  $this->FriendRequestModel->deleterequest($request_id);
                return $this->output->set_status_header(200)
                                    ->set_content_type('application/json')
                                    ->set_output(json_encode(['status' => 'success', 'message' => 'Request accepted successfully.','id' => $sender_id]));
            }   
            else{
                $this->FriendRequestModel->deleterequest($sender_id, $receiver_id);
                return $this->output->set_status_header(200)
                                    ->set_content_type('application/json')
                                    ->set_output(json_encode(['status' => 'success', 'message' => 'Request rejected successfully.']));

        }
        } else {
            return $this->output->set_status_header(500)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to process request.']));
        }
    }

    
// lcaohost?k=5
//$var = $this->input->get('k');
///$var2 = this-inut-get('z');
    /**
     * Get Friend List of User
     */
    public function getFriends($userId) {
        // Validate user ID
        if (!is_numeric($userId)) {
            return $this->output->set_status_header(400)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid user ID.']));
        }
    
        // Retrieve optional filters from the GET request
        $filters = $this->input->get();
    
        // Fetch the friends list
        $friends = $this->FriendRequestModel->getFriendsList($userId);
    
        // Apply filters if present
        if (!empty($filters)) {
            $friends = array_filter($friends, function ($friend) use ($filters) {
                foreach ($filters as $key => $value) {
                    if (isset($friend[$key]) && strcasecmp($friend[$key], $value) !== 0) {
                        return false;
                    }
                }
                return true;
            });
        }
    
        // Add the full profile photo URL
        foreach ($friends as &$friend) {
            $friend['profile_photo'] = base_url() . $friend['profile_photo'];
        }
    
        return $this->output->set_status_header(200)
                            ->set_content_type('application/json')
                            ->set_output(json_encode(['status' => 'success', 'data' => array_values($friends)]));
    }

    public function getFriendRequestStatus() {
        $data = json_decode(file_get_contents('php://input'), true);
    
        $sender_id = $data['sender_id']; // request_id source id
        $receiver_id = $data['receiver_id']; // user_id 
    
        // Validate sender_id
        if (!is_numeric($sender_id)) {
            return $this->output->set_status_header(400)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid sender ID.']));
        }
    
        // Validate receiver_id
        if (!is_numeric($receiver_id)) {
            return $this->output->set_status_header(400)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid receiver ID.']));
        }
    
        // Fetch friend request status
        $requests_status = $this->FriendRequestModel->getFriendRequest($sender_id, $receiver_id);
        // Handle the response based on the status
        if ($requests_status) {
            return $this->output->set_status_header(200)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'success', 'data' => $requests_status]));
        } else {
            
            $requests_status1 = $this->FriendRequestModel->getFriendRequest($receiver_id, $sender_id);
            if ($requests_status1) {
                return $this->output->set_status_header(200)
                                    ->set_content_type('application/json')
                                    ->set_output(json_encode(['status' => 'success', 'data' => $requests_status1]));
            } else {
                return $this->output->set_status_header(404)
                                    ->set_content_type('application/json')
                                    ->set_output(json_encode(['status' => 'success', 'data' => 'send Request']));
            }
            
        }
    
    


    }
    
}

?>
