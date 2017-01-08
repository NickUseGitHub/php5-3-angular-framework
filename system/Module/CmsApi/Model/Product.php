<?php

namespace Application\Module\CmsApi\Model;

use Application\Util\Database;
use Application\Library\Dependency\IDependency;

/**
* 
*/
class Product implements IDependency
{

	private $db;
	
	private static $configDb = array(
   	 		// required credentials

			'host'       => 'mysql',
			'user'       => 'xerox',
			'password'   => 'xerox',
			'database'   => 'xerox',

    		// optional

			'fetchMode'  => \PDO::FETCH_ASSOC,
			'charset'    => 'utf8',
			'options'    => array(
								"port" => 3306,
								'unixSocket' => null
							),
		);
	public static $table = "product";

	public function __construct()
	{
		$this->db = new Database(self::$configDb);
	}
	public function __destruct()
	{
		unset($this->db);
	}

	public function getRows($sql)
	{
		return $this->db->getRows($sql);
	}

	public function insert($data)
	{
		return $this->db->insert(self::$table, $data);
	}

	public function insertMany($data)
	{
		return $this->db->insertMany(self::$table, $data);	
	}

	public function update($conds, $data)
	{
		return !$this->db->update(self::$table, $conds, $data);
	}

	public function updateMany($datas, $uniqueKey)
	{
		return $this->db->updateMany(self::$table, $datas, $uniqueKey);
	}

	public function delete($con)
	{
		return $this->db->delete(self::$table, $con);
	}

	public function executeSql($sql)
	{
		return $this->db->executeSql($sql);
	}
}