<?php

namespace Application\Library\Proxy;

use Application\Library\Dependency\IDependency;
use Application\Util\Session;

/**
* 
*/
class Content
{
	const TABLE_NAME = "content";

	public static function getData(IDependency $model, $id)
	{
		if(empty($id)){
			return null;
		}

		$table = self::TABLE_NAME;
		$sql = "select * from {$table} where id = '{$id}'";
		$result = $model->getRows($sql);
		return empty($result) || empty($result[0])? null : $result[0];
	}

	public static function get(IDependency $model, &$paramCond)
	{
		$category_id = $paramCond['category_id'];
		$search = $paramCond['search'];
		$page = $paramCond['page'];
		$amount_per_page = $paramCond['amount_per_page'];

		$where_kewword = empty($search)? "" : " AND name like '%{$search}%'";

		$table = self::TABLE_NAME;

		$sql = "SELECT ceil(count(*)/{$amount_per_page}) as total_page
				FROM {$table}
				WHERE 
					category_id = '{$category_id}'
					{$where_kewword}";

		$paramCond['total_page'] = $model->getRows($sql)[0]['total_page'];

		$page = ($page - 1)*$amount_per_page;
		$sql = "select *
				from {$table}
				where 
					category_id = '{$category_id}'
					{$where_kewword}
				order by order_id asc
				LIMIT {$page}, {$amount_per_page}";

		$result = $model->getRows($sql);
		
		return $result;
	}

	public static function add(IDependency $model, $data)
	{
		if(empty($model)){
			return null;
		}


		if(!self::orderUpBeforeAdd($model, $data['category_id']))
			return null;

		$current_time = date('Y-m-d H:i:s');

		$data['order_id'] = 1;
		$data['create_by'] = $data['author'];
		$data['update_by'] = $data['author'];
		$data['create_date'] = $current_time;
		$data['update_date'] = $current_time;
		unset($data['author']);
		$id = $model->insert($data);

		return $id;
	}

	public static function update(IDependency $model, $data)
	{
		if(empty($model)){
			return null;
		}

		$current_time = date('Y-m-d H:i:s');

		$data['create_by'] = $data['author'];
		$data['update_by'] = $data['author'];
		$data['create_date'] = $current_time;
		$data['update_date'] = $current_time;
		unset($data['author']);
		return $model->update([ "id" => $data["id"] ], $data);
	}

	public static function updateMany(IDependency $model, $datas, $author)
	{
		if(empty($model)){
			return false;
		}

		$current_time = date('Y-m-d H:i:s');

		foreach ($datas as $key => $data) {
			$datas[$key]['update_by'] = $author;
			$datas[$key]['update_date'] = $current_time;
		}
		return $model->updateMany($datas, "id");
	}

	public static function delete(IDependency $model, $con)
	{
		return $model->delete($con);
	}

	public static function toggleStatus(IDependency $model, $data){

		if(empty($data['id']))
			throw new \Exception("Content::toggleStatus(".__LINE__.") id is null or empty.", 1);
		// if(empty($data['update_by']))
		// 	throw new \Exception("Content::toggleStatus(".__LINE__.") update_by is null or empty.", 1);

		$id = $data['id'];
		$update_by = $data['update_by'];
		$current_time = date('Y-m-d H:i:s');

		$table = self::TABLE_NAME;

		$sql = "update {$table} 
					set 
							status = CASE status 
							  WHEN 0 THEN 1
							  WHEN 1 THEN 0
							END
						,	update_date = '{$current_time}'
						,	update_by = '{$update_by}'
				where id = '{$id}'";

		return $model->executeSql($sql);
	}

	private static function orderUpBeforeAdd(IDependency $model, $category_id){
		if(empty($category_id))
			return false;

		$table = self::TABLE_NAME;
		$sql = "update {$table} set order_id = order_id + 1 where category_id = '{$category_id}'";
		return $model->executeSql($sql);
	}

}