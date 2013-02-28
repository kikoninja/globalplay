<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Users extends MY_Model{

	public function __construct(){
        parent::__construct();
    }
	
	public function get_or_create($user){
		if(isset($user['provider_id'])){
			$sql = "SELECT * FROM user_login JOIN users ON users.id = user_id WHERE provider_id = ? LIMIT 1";
			$dbuser = $this->db->query($sql, $user['provider_id'])->row_array();
		}
		elseif(isset($user['id'])){
			$sql = "SELECT * FROM user_login JOIN users ON users.id = user_id WHERE users.id = ? LIMIT 1";
			$dbuser = $this->db->query($sql, $user['id'])->row_array();
		}

		if(empty($dbuser)){
			$sql = "INSERT INTO users (first_name, last_name, display_name, nickname, thumbnail, gender) 
					VALUES ('{$user['first_name']}', '{$user['last_name']}', '{$user['display_name']}', '{$user['nickname']}', '{$user['thumbnail']}', '{$user['gender']}')";
			$this->db->query($sql);
			$id = $this->db->insert_id();
			$sql = "INSERT INTO user_login (user_id, login_provider, provider_id, access_token, refresh_token) 
					VALUES ('{$id}', '{$user['login_provider']}', '{$user['provider_id']}', '{$user['access_token']}', '{$user['refresh_token']}')";
			$this->db->query($sql);
			$dbuser = $user;
			$dbuser['id'] = $id;
		}

		return $dbuser;
	}

	public function get($provider_id){
		$sql = "SELECT * FROM user_login JOIN users ON users.id = user_id WHERE provider_id = ? LIMIT 1";
		return $this->db->query($sql, $provider_id)->row_array();
	}

	public function get_by_id($id){
		$sql = "SELECT * FROM user_login JOIN users ON users.id = user_id WHERE users.id = ? LIMIT 1";
		return $this->db->query($sql, $id)->row_array();
	}

	public function create($user){
		$sql = $this->db->insert_string('users', array_filter($user['users']));
		$this->db->query($sql);
		
		$user['users']['id'] = $this->db->insert_id();
		$user['user_login']['user_id'] = $user['users']['id'];
		
		$sql = $this->db->insert_string('user_login', array_filter($user['user_login']));
		$this->db->query($sql);

		return $this->get_by_provider_id($user['user_login']['provider_id']);
	}

	public function update($user, $where){
		return $this->save('users', $user, $where);
	}

	public function update_login($user_login, $where){
		return $this->save('user_login', $user_login, $where);
	}

}