<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Playlist extends MY_Controller {

	public function save_title(){
		if(!$this->input->is_ajax_request())
			return;

		$this->load->model('lists');
		$list['name'] = $this->input->post('name');
		$this->lists->update($list, "id = '{$this->input->post('id')}'");
	}

}