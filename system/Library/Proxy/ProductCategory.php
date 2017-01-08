<?php

namespace Application\Library\Proxy;

use Application\Library\Dependency\IDependency;

class ProductCategory
{
    const TABLE_NAME = "product_category";

    public static function get(IDependency $model, $id)
	{
		$table = self::TABLE_NAME;

		$sql = "select * from {$table} where id = '{$id}'";
		$data = $model->getRows($sql);
		return empty($data) || !isset($data[0])? null : $data[0];
	}

    public static function getList(IDependency $model, &$params)
    {

        $parent_id = $params['parent_id'];
		$search = $params['search'];
		$page = $params['page'];
		$amount_per_page = $params['amount_per_page'];

		$where_parent_id = empty($parent_id)? "parent_id is null" : "parent_id = '{$parent_id}'";
		$where_kewword = empty($search)? "" : " AND name like '%{$search}%'";

		$table = self::TABLE_NAME;

		$sql = "SELECT ceil(count(*)/{$amount_per_page}) as total_page
				FROM {$table}
				WHERE 
					{$where_parent_id}
					{$where_kewword}";

		$params['total_page'] = $model->getRows($sql)[0]['total_page'];

		$page = ($page - 1)*$amount_per_page;
		$sql = "select *
				from {$table}
				where 
					{$where_parent_id}
					{$where_kewword}
				order by order_id asc
				LIMIT {$page}, {$amount_per_page}";

		$result = $model->getRows($sql);
		
		return $result;
	}

    public static function getChildren(IDependency $model, $parent_id)
	{
		$table = self::TABLE_NAME;

		$sql = "SELECT id 
				FROM {$table}
				WHERE 
					parent_id = '{$parent_id}'";

		$result = $model->getRows($sql);
		return $result;

	}

    public static function createOrUpdate(IDependency $model, $data, $author){
		
		if(array_key_exists('id', $data) && !empty($data['id']))
			return self::update($model, $data, $author);

		return self::add($model, $data, $author);

	}

    private static function orderUpBeforeAdd(IDependency $model, $parent_id){

		$table = self::TABLE_NAME;

		$sql = "update {$table} set order_id = order_id + 1 where parent_id = '{$parent_id}'";
		return $model->executeSql($sql);

	}

    private static function add(IDependency $model, $data, $author)
	{
		if(empty($model)){
			return null;
		}

		if (!isset($data['parent_id']) || empty($data['parent_id'])) {
			unset($data['parent_id']);
		}else {
			self::orderUpBeforeAdd($model, $data['parent_id']);
		}

		$current_time = date('Y-m-d H:i:s');

		$data['order_id'] = 1;
		$data['create_by'] = $author;
		$data['update_by'] = $author;
		$data['create_date'] = $current_time;
		$data['update_date'] = $current_time;
		$id = $model->insert($data);

		return $id;
	}

    private static function update(IDependency $model, $data, $author)
	{
		if(empty($model)){
			return null;
		}

		if (!isset($data['parent_id']) || empty($data['parent_id'])) {
			unset($data['parent_id']);
		}

		$current_time = date('Y-m-d H:i:s');

		$data['create_by'] = $author;
		$data['update_by'] = $author;
		$data['create_date'] = $current_time;
		$data['update_date'] = $current_time;
		return $model->update([ "id" => $data["id"] ], $data);
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

    private static function delete(IDependency $model, $con)
	{
		return $model->delete($con);
	}

    public static function deleteObj(IDependency $model, $id, $path_file)
	{
		//get object from id
		$product_category_obj = self::get($model, $id);
		if(empty($product_category_obj))
			return false;

		//get children
		$product_category_children_list = self::getChildren($model, $id);

		//delete children iteration
		if(!empty($product_category_children_list)){
			foreach ($product_category_children_list as $key => $product_category_children_obj) {
				
				$children_id = $product_category_children_obj['id'];
				self::deleteObj($model, $children_id, $path_file);
			}
		}

		//delete self
		try{

			self::deteleFile($product_category_obj, $path_file);
			self::delete($model, ['id' => $product_category_obj['id']]);
		}catch (\Exception $e){
			// $response->error_msg = $e->getMessage();
            return false;
		}
		//return
		return true;

	}

    private static function deteleFile($product_category_obj, $path_file){

		if(empty($product_category_obj))
			return;

		// $src_file = $product_category_obj['file'];
		$src_thumb = $product_category_obj['thumb'];
		$src_pic = $product_category_obj['pic'];

		//detele file
		// $target = DATA_PATH."uploads/{$path_file}/{$src_file}";
		// if(file_exists($target))
		// 	@unlink($target);

		//detele thumb
		$target = DATA_PATH."uploads/{$path_file}/{$src_thumb}";
		if(file_exists($target))
			@unlink($target);

		//detele pic
		$target = DATA_PATH."uploads/{$path_file}/{$src_pic}";
		if(file_exists($target))
			@unlink($target);

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
}