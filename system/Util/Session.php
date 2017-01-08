<?php

namespace Application\Util;

/**
* 
*/
class Session
{
	private static $constant; 

	public static function __callStatic($name, $arguments)
	{
		if(session_id() == '') {
			session_start();
		}

        $actualMethod = '_'.$name;
        return call_user_func_array(__NAMESPACE__ ."\Session::{$actualMethod}", $arguments);
	}

	public static function _get($key)
	{
		if(empty($key)){
			return;
		}

		return $_SESSION[$key];
	}

	public static function _set($key, $value)
	{
		if(empty($key) || empty($value)){
			return;
		}

		$_SESSION[$key] = $value;
	}

	public static function _delete($key)
	{
		if(empty($key)){
			return;
		}

		unset($_SESSION[$key]);
	}

}