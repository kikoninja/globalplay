<?php
 
class MY_Controller extends CI_Controller {
 
	private $content_areas;
 
	function __construct(){
		parent::__construct();
		if(get_session('user')&&!get_cookie('access_token')){
			// redirect(base_url('auth/google/authorize/'.str_replace('/', '|', current_url())));
		}
	}
 
	function add_view($content_area, $view, $data = array()){
		$this->add_content($content_area, $this->load->view($view, $data, TRUE));
	}
 
	function add_content($content_area, $content){
		$this->content_areas[$content_area] = $content;
	}
 
	function render($data = array(), $layout = "default"){
		$this->content_areas = array_merge($this->content_areas, $data);
		$this->load->view('layouts/'.$layout, $this->content_areas);
	}
 
}