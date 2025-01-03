<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EducationController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('EducationDetailsModel');
        $this->load->library('form_validation');
    }

    // Add education details
    public function addEducationDetails() {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
            return;
        }

        $data = $this->input->post();

        $this->form_validation->set_rules('college_school', 'College/School', 'required');
        $this->form_validation->set_rules('degree_program', 'Degree Program', 'required');
        $this->form_validation->set_rules('start_year', 'Start Year', 'required');
        $this->form_validation->set_rules('end_year', 'End Year', 'required');
        $this->form_validation->set_rules('is_current', 'Is Current', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }

        $result = $this->EducationDetailsModel->fillEducationDetails(
            $data['college_school'],
            $data['degree_program'],
            $data['start_year'],
            $data['end_year'],
            $data['is_current']
        );

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Education details added successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add education details.']);
        }
    }

    // Remove education details
    public function removeEducationDetails($id) {
        $result = $this->EducationDetailsModel->removeEducationDetails($id);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Education details removed successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to remove education details.']);
        }
    }

    // Update education details
    public function updateEducationDetails($id) {
        $data = $this->input->post();

        $this->form_validation->set_rules('college_school', 'College/School', 'required');
        $this->form_validation->set_rules('degree_program', 'Degree Program', 'required');
        $this->form_validation->set_rules('start_year', 'Start Year', 'required');
        $this->form_validation->set_rules('end_year', 'End Year', 'required');
        $this->form_validation->set_rules('is_current', 'Is Current', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }

        $result = $this->EducationDetailsModel->updateEducationDetails(
            $id,
            $data['college_school'],
            $data['degree_program'],
            $data['start_year'],
            $data['end_year'],
            $data['is_current']
        );

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Education details updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update education details.']);
        }
    }
}
?>
