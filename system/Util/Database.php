<?php 
namespace Application\Util;

use Simplon\Mysql\Manager\SqlManager;
use Simplon\Mysql\Manager\SqlQueryBuilder;
use Simplon\Mysql\Mysql;

/**
* Database
*/
class Database
{
	
	protected $dbConn;

	public function __construct($config)
	{
		
		$this->dbConn = new Mysql(
			$config['host'],
		    $config['user'],
		    $config['password'],
		    $config['database'],
		    $config['fetchMode'],
		    $config['charset'],
		    $config['options']
		);

	}

	public function __destruct()
	{
		unset($this->dbConn);
	}

	//TODO:: fix this function
	public function getRows($sql)
	{

		if(empty($this->dbConn))
			return null;

		return $this->dbConn->fetchRowMany($sql, array());

	}

	public function insert($table, $data)
	{

		if(empty($table) || empty($data) || !is_array($data))
			return null;

		return $this->dbConn->insert($table, $data);
	}

	public function insertMany($table, $data)
	{

		if(empty($table) || empty($data) || !is_array($data))
			return null;

		$sqlBuilderObj = new SqlQueryBuilder();
		$sqlBuilder = $sqlBuilderObj
					    ->setTableName($table)
					    ->setData($data);

		$sqlManager = new SqlManager($this->dbConn);

		return $sqlManager->insert($sqlBuilder);
	}

	public function update($table, $conds, $data)
	{

		if(empty($table) || empty($conds) || !is_array($conds) || empty($data) || !is_array($data))
			return false;

		return !$this->dbConn->update($table, $conds, $data);

	}

	public function updateMany($table, $datas, $uniqueKey)
	{
		if (empty($table) || empty($datas) || !is_array($datas) || empty($uniqueKey)) {
			return false;
		}

		$sql = "";
		foreach ($datas as $data) {
			if(!array_key_exists($uniqueKey, $data)){
				continue;
			}

			$dataUniqueKey = $data[$uniqueKey];
			unset($data[$uniqueKey]);
			$data_keys = array_keys($data);
			$strAttr = "(" . implode(",", $data_keys) . ")";

			$strUpdate = array();
			foreach ($data as $key => $value) {
				$strUpdate[] = sprintf("%s='%s'", $key, $value);
			}
			$strUpdate = implode(",", $strUpdate);

			$uniqueValue = $data[$uniqueKey];
			$sql .= "update {$table} set {$strUpdate} where {$uniqueKey} = {$dataUniqueKey};";

		}

		return $this->executeSql($sql);

	}

	public function delete($table, $con)
	{

		if(empty($table) || empty($con) || !is_array($con))
			return false;

		return !$this->dbConn->delete($table, $con);
	}

	public function executeSql($sql)
	{

		if(empty($sql))
			return false;

		return $this->dbConn->executeSql($sql);

	}

}