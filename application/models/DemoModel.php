<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class demoModel extends CI_Model {
    
    // Constructor to load the database
    public function __construct() {
        parent::__construct();
        $this->load->database();  // Load the database
    }

    // Function to retrieve all employees
    public function get_all_employees() {
        $query = $this->db->get('Employees');  // Get all records from the Employees table
        return $query->result();  // Return the result as an array of objects
    }

    // Function to retrieve an employee by ID
    public function get_employee_by_id($employee_id) {
        $this->db->where('EmployeeID', $employee_id);  // Apply condition for EmployeeID
        $query = $this->db->get('Employees');  // Get the record with the specified EmployeeID
        return $query->row();  // Return the result as a single object
    }

    // Function to retrieve employees by department
    public function get_employees_by_department($department) {
        $this->db->where('Department', $department);  // Apply condition for Department
        $query = $this->db->get('Employees');  // Get the records for the given department
        return $query->result();  // Return the result as an array of objects
    }

    // Function to retrieve employees within a specific salary range
    public function get_employees_by_salary_range($min_salary, $max_salary) {
        $this->db->where('Salary >=', $min_salary);  // Apply condition for minimum salary
        $this->db->where('Salary <=', $max_salary);  // Apply condition for maximum salary
        $query = $this->db->get('Employees');  // Get the records for the given salary range
        return $query->result();  // Return the result as an array of objects
    }
}
