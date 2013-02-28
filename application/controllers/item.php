<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Item extends MY_Controller {

	public function add(){
		if(!$this->input->is_ajax_request())
			return;

		$this->load->model('items');
		echo $this->items->create($this->input->post());
	}

	public function remove(){
		if(!$this->input->is_ajax_request())
			return;

		$this->load->model('items');
		$this->items->delete($this->input->post('id'));
	}

	public function sort(){
		if(!$this->input->is_ajax_request())
			return;

		$this->load->model('items');
		$this->items->sort(array('id'=>$this->input->post('id'), 'position'=>$this->input->post('position')));
	}

}