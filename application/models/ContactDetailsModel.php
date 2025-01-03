<?php 
define('BASEPATH') or exit('No direct scripts is allowed');

class ContactDetailsModel extends CI_Model{

    public function fillContactDetails($primary_phone,$linkedin_url,$alternative_email,$alternative_phone){

        $data = array(
            '$primary_phone' => $primary_phone,
            '$linkedin_url' => $linkedin_url,
            '$alternative_email' => $alternative_email,
            '$alternative_phone' => $alternative_phone,
        )
        return $this->db->insert('contact_details', $data);
    }

}


?>