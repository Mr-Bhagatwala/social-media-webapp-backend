<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_Model extends CI_Model {

    // Register new user
    public function register($name, $email, $password)
    {
        $data = array(
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'profile_photo' => "",
            'gender' => "",
            'marital_status' => "",
            'date_of_birth' => "",
            'current_city' => "",
            'hometown' => ""
        );
    
        $this->db->insert('users', $data);
    
        return $this->db->insert_id(); 
    }
    
    // Login user by email
    public function login($email)
    {
       
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        // $query = $this->db->from('users')->where('email', $email)->get();
        if ($query->num_rows() == 1)
        {
            return $query->row_array(); // Return user data
        }
        else
        {
            return FALSE;
        }
    }
    public function fillDetails($profile_photo, $gender ,$marital_status ,$date_of_birth, $current_city, $hometown) {
        $data = array(
            'profile_photo' => $profile_photo,
            'gender' => $gender,
            'marital_status' => $marital_status,
            'date_of_birth' => $date_of_birth,
            'current_city' => $current_city,
            'hometown' => $hometown,
        ); // Fixed: Added semicolon here
        return $this->db->insert('users', $data);
    }
    
    public function getUserDetail($user_id){
        $this->db->where('id', $user_id);
        $query = $this->db->get('users');
        return $query->result_array();
    }

    public function searchUsersM($name){
        $this->db->select('id as userId, name');
        $this->db->from('users'); 
        $this->db->like('name', $name);
        $query = $this->db->get();
    
        return $query->result_array();
    }
    
    public function updateBasicDetails($userId, $name, $bio, $hometown) {
        $data = [
            'name' => $name,
            'bio' => $bio,
            'hometown' => $hometown,
        ];
    
        $this->db->where('id', $userId);
        return $this->db->update('users', $data); // Update only basic details
    }    
    
}
?>
