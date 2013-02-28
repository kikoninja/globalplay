<?php

function curl($options){
	$defaults = array(
		CURLOPT_RETURNTRANSFER=>1,
		CURLOPT_FOLLOWLOCATION=>1,
		CURLOPT_SSL_VERIFYPEER=>0
	);
	$options = $options+$defaults;

	$ch = curl_init();
	curl_setopt_array($ch, $options);
	$ret = curl_exec($ch);
	curl_close($ch);
	
	return $ret;
}

if(!function_exists('get_session')){
	function get_session($key){
		$CI =& get_instance();
		return $CI->session->userdata($key);
	}
}

if(!function_exists('set_session')){
	function set_session($data){
		$CI =& get_instance();
		return $CI->session->set_userdata($data);
	}
}

if(!function_exists('del_session')){
	function del_session($data){
		$CI =& get_instance();
		return $CI->session->unset_userdata($data);
	}
}

function get_id($len=11){
	$string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-_';
	$ret = '';
	while(strlen($ret)<$len){
		$ret .= substr($string, rand(0,63), 1);
	}
	return $ret;
}