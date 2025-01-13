<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');  // Allow all origins
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');  // Allow these methods
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
class DemoController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('DemoModel');  // Load the Employee model
    }

    // API method to get all employees as JSON
    public function get_all_employees() {
        // Set the content type header to application/json
        $this->output->set_content_type('application/json');

        // Retrieve all employees from the Employee model
        $employees = $this->DemoModel->get_all_employees();

        // Check if employees data exists
        if ($employees) {
            // Return the data as JSON with a success status
            $response = [
                'status' => 'success',
                'data' => $employees
            ];
        } else {
            // If no data found, return an error message
            $response = [
                'status' => 'error',
                'message' => 'No employees found.'
            ];
        }

        // Send the response as JSON
        $this->output->set_output(json_encode($response));
    }
}
