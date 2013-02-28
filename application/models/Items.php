<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Items extends MY_Model{

	public function __construct(){
        parent::__construct();
    }

    public function create($item){
    	$id = $this->insert('items', $item);
    	$item['id'] = $id;
    	$this->sort($item);

    	return $id;
    }

    public function update($item, $where){
    	return $this->save('items', $item, $where);
    }

    public function delete($id){
    	$item = $this->get($id);
    	$item['position'] = '9999';
    	$this->sort($item);

    	$sql = "DELETE FROM items WHERE id = ?";
    	$this->db->query($sql, $id);
    	return $this->db->affected_rows();
    }

    public function get($id){
    	$sql = "SELECT * FROM items WHERE id = ?";
    	return $this->db->query($sql, $id)->row_array();
    }

    public function sort($item){
    	$sql = "SELECT * FROM items WHERE id = ?";
    	$old = $this->db->query($sql, $item['id'])->row_array();

    	if($item['position']==$old['position']){
			$sql = "UPDATE items SET position = position + 1 WHERE list_id = ? AND position >= ?";
	    	$this->db->query($sql, array($old['list_id'], $item['position']));
    	}
    	else{
    		if($item['position']<$old['position'])
	    		$sql = "UPDATE items SET position = position + 1 WHERE list_id = ? AND position >= ? AND position < ?";
	    	else
	    		$sql = "UPDATE items SET position = position - 1 WHERE list_id = ? AND position <= ? AND position > ?";
	    	$this->db->query($sql, array($old['list_id'], $item['position'], $old['position']));
	    }

    	$sql = "UPDATE items SET position = ? WHERE id = ?";
    	$this->db->query($sql, array($item['position'], $item['id']));
    }

}