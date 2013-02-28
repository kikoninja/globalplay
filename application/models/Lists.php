<?php

class Lists extends MY_Model{

	public function __construct(){
        parent::__construct();
    }

	public function get($id){
		$sql = 'SELECT * FROM lists WHERE id = ?';
		return $this->db->query($sql, $id)->row_array();
	}

    public function create($list){
    	$list['id'] = $this->get_id();
    	return $this->insert('lists', $list);;
    }

    public function update($list, $where){
    	return $this->save('lists', $list, $where);
    }

    public function delete($id){
    	$sql = "DELETE FROM lists WHERE id = ?";
    	$this->db->query($sql, $id);
    	return $this->db->affected_rows();
    }

    public function user_lists($user_id){
    	$sql = "SELECT * FROM lists WHERE user_id = ?";
    	return $this->db->query($sql, $user_id)->result_array();
    }

    public function get_items($id){
    	$sql = "SELECT * FROM items WHERE list_id = ? ORDER BY position";
    	return $this->db->query($sql, $id)->result_array();
    }

    public function get_id(){
    	$id = get_id();
    	$res = $this->get($id);
    	while(!empty($res)){
    		$id = get_id();
    		$res = $this->get($id);
    	}
    	return $id;
    }

}