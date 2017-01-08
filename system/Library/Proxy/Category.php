<?php

namespace Application\Library\Proxy;

use Application\Library\Dependency\IDependency;
use Application\Util\Session;

/**
* 
*/
class Category
{
	const TABLE_NAME = "category";

	public static function getDataByCode(IDependency $model, $code)
	{
		if(empty($code)){
			return null;
		}

		$table = self::TABLE_NAME;
		$sql = "select * from {$table} where code = '{$code}' limit 1";
		$result = $model->getRows($sql);

		return empty($result)? null : $result[0];
	}

}