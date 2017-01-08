<?php

namespace Application\Library\Proxy;

use Application\Library\Dependency\IDependency;
use Application\Util\Session;

/**
* 
*/
class Admin
{
	
	//User types
	const TYPE_SUPER_ADMIN = 0;
	const TYPE_ADMIN = 1;
	const TYPE_STAFF = 2;

	const SESS_KEY = "admin_token";
	const HASHKEY = "xorex_admin";
	const TOKEN_TIME_LIMIT = 30;

	public static function getTypeSuperAdmin()
	{
		return self::TYPE_SUPER_ADMIN;
	}

	public static function getTypeAdmin()
	{
		return self::TYPE_ADMIN;
	}

	public static function getTypeStaff()
	{
		return self::TYPE_STAFF;
	}

	public static function addAdmin(IDependency $model, $data)
	{
		if(empty($model)){
			return null;
		}

		$current_time = date('Y-m-d H:i:s');

		$data['password'] = self::encryptPassword($data['password']);
		$data['create_by'] = 0;
		$data['update_by'] = 0;
		$data['create_date'] = $current_time;
		$data['update_date'] = $current_time;
		$id = $model->insert($data);

		return $id;
	}

	public static function getTokenFromSession()
	{
		return Session::get(self::SESS_KEY);
	}

	public static function isLogin(){
		$token = self::getTokenFromSession();
		return !empty($token);
	}

	public static function logout(){
		Session::delete(self::SESS_KEY);
		return true;
	}

	public static function login(IDependency $adminModel, IDependency $tokenModel, $username, $password, $ip)
	{
		$user = self::getUserByUsernamePassword($adminModel, $username, $password);
		if(empty($user)){
			return null;
		}

		$userId = $user[0]['id'];
		//BUG:: this line make only one person can login
		self::deleteTokenByUserId($tokenModel, $userId);
		$token = self::generateTokenKey($tokenModel, $userId, $ip);

		return $token;
	}

	public static function getUserFromToken(IDependency $model, $token)
	{	
		if(empty($token)){
			return null;
		}

		$sql = "select ad.* 
				from admin ad
				inner join admin_token at on at.admin_id = ad.id
				where token = '{$token}'";
		$user = $model->getRows($sql);
		return empty($user)? null : $user[0];
	}

	private static function getUserByUsernamePassword(IDependency $model, $username, $password)
	{
		if(empty($username)){
			return null;
		}
		if(empty($password)){
			return null;
		}

		$password = self::encryptPassword($password);
		$sql = "select * from admin where username = '{$username}' and password = '{$password}' limit 1";
		$user = $model->getRows($sql);
		return $user;
	}

	private static function deleteTokenByUserId(IDependency $model, $userId)
	{
		return $model->delete(array('admin_id' => $userId));
	}

	private static function deleteTokenByKey(IDependency $model, $token)
	{
		return $model->delete(array('token' => $token));
	}

	private static function generateTokenKey(IDependency $model, $userId, $ip)
	{
		$token = self::encryptPassword(uniqid($userId, true));

		$current_time = date('Y-m-d H:i:s');
		$data = array();
		$data['token'] = $token;
		$data['admin_id'] = $userId;
		$data['update_date'] = $current_time;
		$data['ip'] = $ip;
		$id = $model->insert($data);

		if(empty($id)){
			return null;
		}

		return $token;

	}

	public static function tokenIsExpired(IDependency $model, $token)
	{
		$tokenObj = self::getTokenByTokenKey($model, $token);
		if(empty($tokenObj)){
			return null;
		}

		$lastUpdate = $tokenObj[0]['update_date'];
		$current_time = date('Y-m-d H:i:s');

		$datetime1 = strtotime($lastUpdate);
		$datetime2 = strtotime($current_time);
		$interval  = abs($datetime2 - $datetime1);
		$minutes   = round($interval / 60);

		return $minutes > self::TOKEN_TIME_LIMIT;
	}

	private static function getTokenByTokenKey(IDependency $model, $token)
	{
		if(empty($token)){
			return null;
		}

		$sql = "select * from admin_token where token = '{$token}'";
		$token = $model->getRows($sql);
		return $token;
	}

	private static function encryptPassword($password){

		$key = self::HASHKEY;
	
		$password = hash('sha512', $key.$password);
		$password = md5($password);

		return $password;
	}
}