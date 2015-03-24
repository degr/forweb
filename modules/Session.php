<?php 

class Session{

	public static function set($key, $value){
		$_SESSION[$key] = $value;
	}
	
	public static function get($key){
		return $_SESSION[$key];
	}

	public static function contain($key){
		return isset($_SESSION[$key]);
	}
	
	public static function clear(){
		unset($_SESSION);
	}
}