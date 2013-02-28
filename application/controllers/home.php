<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

	public function index($list=false)
	{
		$this->load->model('users');
		$this->load->model('lists');
		if(!$list){
			if(!get_session('user')){
				$list['id'] = $this->lists->get_id();
				$list['name'] = 'New List '.date('Y-m-d');
			}
			else{
				$user = get_session('user');
				$lists = $this->lists->user_lists($user['id']);
				if(empty($lists)){
					$list['name'] = 'New List '.date('Y-m-d');
					$list['user_id'] = $user['id'];
					$list['created'] = date(MYSQL_DATE_FORMAT);
					$id = $this->lists->create($list);
					$list = $this->lists->get($id);
				}
				else{
					$list = $lists[0];
					$list['items'] = $this->lists->get_items($list['id']);
				}
			}
		}
		else{
			if(!get_session('user')){
				$list = $this->lists->get($list);
				if(empty($list)){
					$list['id'] = $this->lists->get_id();
					$list['name'] = 'New List '.date('Y-m-d');
				}
				else
					$list['items'] = $this->lists->get_items($list['id']);
			}
			else{
				$user = get_session('user');
				$list = $this->lists->get($list);
				if(empty($list)){
					$list['name'] = 'New List '.date('Y-m-d');
					$list['user_id'] = $user['id'];
					$list['created'] = date(MYSQL_DATE_FORMAT);
					$id = $this->lists->create($list);
					$list = $this->lists->get($id);
				}
				else
					$list['items'] = $this->lists->get_items($list['id']);
			}
		}

		$this->add_view('main', 'home/index', compact('list'));
		$this->render();
	}
}