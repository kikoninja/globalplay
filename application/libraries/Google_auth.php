<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Google_auth{

	private $API_KEY = 'AIzaSyAf_iglqYWK0lMWZJfaY51jAHwQXYkD6XA';
	private $CLIENT_ID = '564452369939.apps.googleusercontent.com';
	private $CLIENT_SECRET = '7Xbp8Iftx0S7ZsO-Nfr3_ROT';

	public function __construct(){

	}

	public function authenticate($approval='auto'){
		$url = 'https://accounts.google.com/o/oauth2/auth';
		$url .= '?client_id='.$this->CLIENT_ID;
		$url .= '&redirect_uri='.base_url().'auth/google/callback';
		$url .= '&response_type=code&scope=https://www.googleapis.com/auth/userinfo.profile&access_type=offline&approval_prompt='.$approval;

		redirect($url);
	}

	public function authorize($code){
		$url = 'https://accounts.google.com/o/oauth2/token';
		$data = array(
			'code'=>$code,
			'client_id'=>$this->CLIENT_ID,
			'client_secret'=>$this->CLIENT_SECRET,
			'redirect_uri'=>base_url().'auth/google/callback',
			'grant_type'=>'authorization_code'
		);
		return json_decode($this->send_post($url, $data), true);
	}

	public function get_user($access_token){
		$url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token='.$access_token;
		return json_decode(curl(array(CURLOPT_URL=>$url)), true);
	}

	public function refresh($refresh_token){
		$url = 'https://accounts.google.com/o/oauth2/token';
		$data = array(
			'client_id'=>$this->CLIENT_ID,
			'client_secret'=>$this->CLIENT_SECRET,
			'refresh_token'=>$refresh_token,
			'grant_type'=>'refresh_token'
		);
		return json_decode($this->send_post($url, $data), true);
	}

	private function send_post($url, $data){
		$opts = array(
			CURLOPT_URL=>$url, 
			CURLOPT_POST=>1,
			CURLOPT_POSTFIELDS=>$data
		);
		return curl($opts);
	}
}