<?php
 
class MY_Model extends CI_Model {
 
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}
 
	public function save($table, $data, $where){
		$this->db->query($this->db->update_string($table, $data, $where));
		return $this->db->affected_rows();
	}

	public function insert($table, $data){
		$this->db->query($this->db->insert_string($table, $data));
		return $this->db->insert_id();
	}
 
}