<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AlternativePhonesController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('AlternativePhonesModel');
        $this->load->helper('url');
        $this->load->library('form_validation');
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
        header('Access-Control-Allow-Headers: Content-Type, Authorization');  // Allow specific headers
    }

    public function addAlternativePhone() {
        // $user_id = $this->session->userdata('user_id');

        // if (!$user_id) {
        //     echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
        //     return;
        // }

        // $data = $this->input->post();
        $data = json_decode(file_get_contents('php://input'), true);
        // $this->form_validation->set_rules('alternative_phone', 'Alternative Phone', 'required|numeric|min_length[10]|max_length[15]');

        // if ($this->form_validation->run() == FALSE) {
        //     $response = ['status' => 'error', 'message' => validation_errors()];
        //     echo json_encode($response);
        //     return;
        // }

        $result = $this->AlternativePhonesModel->addAlternativePhone($data['user_id'], $data['alternative_phones']);
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Alternative phone added successfully.']);
        } else {    
            echo json_encode(['status' => 'error', 'message' => 'Failed to add alternative phone.']);
        }
    }

    public function removeAlternativePhone() {

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['user_id']) || empty($data['phone'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing user_id or phone.']);
            return;
        }

        $user_id = $data['user_id'];
        $phone = $data['phone'];

        $this->db->where('user_id', $user_id);
        $this->db->where('alternate_phone', $phone);
        $this->db->delete('alternate_phones');

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Alternate phone deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No matching record found.']);
        }
    }

    public function listAlternativePhones() {
        // $user_id = $this->session->userdata('user_id');

        // if (!$user_id) {
        //     echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
        //     return;
        // }
        $user_id = json_decode(file_get_contents('php://input'), true);
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }
        $phones = $this->AlternativePhonesModel->getUserPhones($user_id);
        echo json_encode(['status' => 'success', 'phones' => $phones]);
    }
}
?>
