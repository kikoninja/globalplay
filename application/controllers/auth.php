<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function google($action = false, $referrer = false)
	{
		$this->load->library('google_auth');
		switch($action){
			case 'callback':
				$auth_code = $_GET['code'];
				if(!$auth_code){
					//TODO handle not granting permissions
					redirect(base_url());
				}
				$auth = $this->google_auth->authorize($auth_code);
				if(isset($auth['error'])){
					//TODO handle authorization error
					redirect(base_url());
				}
				if(isset($auth['access_token'])){
					setcookie('access_token', $auth['access_token'], time()+$auth['expires_in'], '/');
					
					$user = $this->google_auth->get_user($auth['access_token']);
					
					$this->load->model("users");
					$existing_user = $this->users->get($user['id']);

					if(empty($existing_user)){
						$img = json_decode(curl(array(CURLOPT_URL=>'https://picasaweb.google.com/data/feed/api/user/'.$user['id'].'?alt=json')), true);
						$user = $this->users->create(array(
							'users'=>array(
								'first_name'=>@$user['given_name'],
								'last_name'=>@$user['family_name'],
								'display_name'=>@$user['name'],
								'nickname'=>@$img['feed']['gphoto$nickname']['$t'],
								'thumbnail'=>@$img['feed']['gphoto$thumbnail']['$t'],
								'gender'=>@$user['gender']
							),
							'user_login'=>array(
								'login_provider'=>'google',
								'provider_id'=>$user['id'],
								'access_token'=>$auth['access_token'],
								'refresh_token'=>@$auth['refresh_token']
							)
						));
					}
					else
						$user = $existing_user;
					
					if(empty($user['refresh_token'])){
						if(isset($auth['refresh_token'])){
							$this->users->update_login(array('refresh_token'=>$auth['refresh_token']), "login_provider = 'google' AND user_id = {$user['id']}");
						}
						else
							setcookie('request_refresh_token', 1, strtotime('+5 years'), '/');
					}

					set_session(array('user'=>array(
						'id'=>$user['id'],
						'provider_id'=>$user['provider_id'],
						'name'=>empty($user['display_name'])?$user['nickname']:$user['display_name'],
						'thumb'=>$user['thumbnail']
					)));
					redirect(base_url());
				}
				break;
			case 'authorize':
				$this->load->model('users');
				$user = get_session('user');
				$user = $this->users->get($user['id']);
				if(empty($user['refresh_token'])){
					setcookie('request_refresh_token', 1, strtotime('+5 years'), '/');
					redirect(base_url('auth/google'));
				}
				$refresh = $this->google_auth->refresh($user['refresh_token']);
				$this->users->update_login(array('access_token'=>$refresh['access_token']), "user_id = {$user['id']}");
				if($this->input->is_ajax_request()){
					header('Content-Type: application/json');
					echo json_encode(array('access_token'=>$refresh['access_token'], 'expires_in'=>$refresh['expires_in']));
					exit;
				}
				else{
					setcookie('access_token', $refresh['access_token'], time()+$refresh['expires_in'], '/');
					if($referrer)
						redirect(str_replace('|', '/', $referrer));
				}
				break;
			default:
				if(get_cookie('request_refresh_token')){
					delete_cookie('request_refresh_token');
					$this->google_auth->authenticate('force');
				}
				$this->google_auth->authenticate();
				break;
		}
	}

	public function logout(){
		delete_cookie('access_token');
		del_session('user');
		redirect(base_url());
	}
}