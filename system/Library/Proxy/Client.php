<?php

namespace Application\Library\Proxy;

use Application\Library\Dependency\IDependency;
use Application\Util\Session;
use Application\Util\StringLib;

/**
* 
*/
class Client
{
	
	//User types
	const TYPE_CLIENT_NOT_REGISTER = 0;
	const TYPE_CLIENT_REGISTERED = 1;

	const SESS_KEY = "client_token";
	const HASHKEY = "emwardtk_client";
	const TOKEN_TIME_LIMIT = 30;

	const TABLE_BILL_NAME = "client";

	public static function get(IDependency $model, &$paramCond)
	{
		$search = $paramCond['search'];
		$page = $paramCond['page']? : 1;
		$amount_per_page = $paramCond['amount_per_page'];

		$where_kewword = empty($search)? "" : " AND name like '%{$search}%'";

		$table = self::TABLE_BILL_NAME;

		$sql = "SELECT ceil(count(*)/{$amount_per_page}) as total_page
				FROM {$table}
				WHERE 1  
					{$where_kewword}
				order by create_date desc";

		$paramCond['total_page'] = $model->getRows($sql)[0]['total_page'];

		$page = ($page - 1)*$amount_per_page;
		$sql = "select *
				from {$table}
				where 1
					{$where_kewword}
				order by create_date desc
				limit {$page}, {$amount_per_page}";

		$result = $model->getRows($sql);
		
		return $result;
	}

	public static function getUser(IDependency $model, $member_id)
	{
		$sql = "select *
				from client
				where id = '{$member_id}'";
		$user = $model->getRows($sql);
		return empty($user) || !isset($user[0])? null : $user[0];
	}

	public static function getTypeClientNotRegister()
	{
		return self::TYPE_CLIENT_NOT_REGISTER;
	}

	public static function getTypeClientRegistered()
	{
		return self::TYPE_CLIENT_REGISTERED;
	}

	public static function getUserObjByEmail(IDependency $model, $email)
	{
		if (empty($email)) {
			return null;
		}
		
		$sql = "select 
					paid_title,
					paid_name,
					paid_lastname,
					paid_email,
					paid_address,
					paid_city,
					paid_postal_code,
					paid_country,
					paid_tel,
					paid_fax,
					paid_mobile,
					shipping_title,
					shipping_name,
					shipping_lastname,
					shipping_email,
					shipping_address,
					shipping_city,
					shipping_postal_code,
					shipping_country,
					shipping_tel,
					shipping_fax,
					shipping_mobile
				from client
				where paid_email = '{$email}'";
		$user = $model->getRows($sql);
		return empty($user) || !isset($user[0])? null : $user[0]; 
	}

	/*** 
	  * param (obj)$model, (array)$data, (number)$author
	  * return $id
	  **/
	public static function register(IDependency $model, $data)
	{
		if (empty($data) || !isset($data['paid_email'])) {
			return 0;
		}

		$author = 0;
		$user_id = self::userExist($model, $data['paid_email']); 
		if (!empty($user_id) && $user_id !== 0) {
			$data['id'] = $user_id;
			if (self::update($model, $data, $author)) {
				return $user_id;
			}else {
				return 0;
			}
		}else {
			return self::add($model, $data, $author);
		}
	}

	private static function userExist(IDependency $model, $email)
	{
		$sql = "select id
				from client
				where paid_email = '{$email}'";
		$user = $model->getRows($sql);
		$user_id = !empty($user) && isset($user[0]) && isset($user[0]['id'])? $user[0]['id'] : 0; 
		return $user_id;
	}

	private static function add(IDependency $model, $data, $author)
	{
		if(empty($model)){
			return 0;
		}

		$current_time = date('Y-m-d H:i:s');
        $password = empty($data['password'])? self::encryptPassword(StringLib::generateRandomString(5)) : self::encryptPassword($data['password']);

		$data['status'] = self::getTypeClientNotRegister();
		$data['password'] = self::encryptPassword($password);
		$data['update_by'] = $author? : 0;
		$data['create_date'] = $current_time;
		$data['update_date'] = $current_time;
		$id = $model->insert($data);

		return $id;
	}

	private static function update(IDependency $model, $data, $author)
	{
		if(empty($model)){
			return true;
		}
		if (!isset($data["id"])) {
			return false;
		}

		$current_time = date('Y-m-d H:i:s');

		$data['update_by'] = $author? : 0;
		$data['update_date'] = $current_time;
		return $model->update([ "id" => $data["id"] ], $data);
	}

	public static function getTokenFromSession()
	{
		return Session::get(self::SESS_KEY);
	}

	public static function isLogin(){
		return !empty(self::getTokenFromSession());
	}

	public static function logout(){
		Session::delete(self::SESS_KEY);
		return true;
	}

	public static function login(IDependency $clientModel, IDependency $tokenModel, $username, $password, $ip)
	{
		$user = self::getUserByUsernamePassword($clientModel, $username, $password);
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
				from client ad
				inner join client_token at on at.client_id = ad.id
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
		$sql = "select * from client where username = '{$username}' and password = '{$password}' limit 1";
		$user = $model->getRows($sql);
		return $user;
	}

	private static function deleteTokenByUserId(IDependency $model, $userId)
	{
		return $model->delete(array('client_id' => $userId));
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
		$data['client_id'] = $userId;
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

		$sql = "select * from client_token where token = '{$token}'";
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