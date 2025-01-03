<?php 
define('BASEPATH') or exit('No direct scripts is allowed');

    class EducationDetailsModel extends CI_Model{
        public function fillWorkDetails($company_organisation,$designation ,$start_year,$end_year,$is_current){
            $data = array(
                '$company_organisation' => $company_organisation,
                '$designation ' => $designation ,
                '$start_year' => $start_year,
                '$end_year' => $end_year,
                '$is_current' => $is_current,
    
            )
            return $this->db->insert('work_history ', $data);
        }
    }
?>