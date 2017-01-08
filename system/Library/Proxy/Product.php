<?php

namespace Application\Library\Proxy;

use Application\Library\Dependency\IDependency;

class Product
{
    const TABLE_NAME = "product";

    public static function get(IDependency $model, $id)
	{
		$table = self::TABLE_NAME;

		$sql = "select * from {$table} where id = '{$id}'";
		$data = $model->getRows($sql);
		return empty($data) || !isset($data[0])? null : $data[0];

		return $data;
	}

    public static function getList(IDependency $model, &$params)
    {

		$product_category_id = $params['product_category_id'];
		$search = $params['search'];
		$page = $params['page'];
		$amount_per_page = $params['amount_per_page'];

		$where_kewword = empty($search)? "" : " AND name like '%{$search}%'";

		$table = self::TABLE_NAME;

		$sql = "SELECT ceil(count(*)/{$amount_per_page}) as total_page
				FROM {$table}
				WHERE 
					product_category_id = '{$product_category_id}'
					{$where_kewword}";

		$params['total_page'] = $model->getRows($sql)[0]['total_page'];

		$page = ($page - 1)*$amount_per_page;
		$sql = "select *
				from {$table}
				where 
					product_category_id = '{$product_category_id}'
					{$where_kewword}
				order by order_id asc
				LIMIT {$page}, {$amount_per_page}";

		$result = $model->getRows($sql);
		return $result;
	}

    public function getDataListByProductCategoryId(IDependency $model, $product_category_id)
	{
		if(empty($product_category_id))
			return null;

		$table = self::TABLE_NAME;

		$sql = "select id, thumb, pic
				from {$table}
				where 
					product_category_id = '{$product_category_id}'";

		$result = $model->getRows($sql);
		return $result;
	}

    public static function createOrUpdate(IDependency $model, $data, $author)
    {
		//validation
		if(empty($data['product_category_id']))
			return null;

		if(array_key_exists('id', $data) && !empty($data['id']))
			return self::update($model, $data, $author);

		return self::add($model, $data, $author);
	}

    private static function add(IDependency $model, $data, $author)
	{
		if(empty($model)){
			return null;
		}

		if (!isset($data['product_category_id']) || empty($data['product_category_id'])) {
			return null;
		}else {
			self::orderUpBeforeAdd($model, $data['product_category_id']);
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

		if (!isset($data['product_category_id']) || empty($data['product_category_id'])) {
			return null;
		}

		$current_time = date('Y-m-d H:i:s');

		$data['create_by'] = $author;
		$data['update_by'] = $author;
		$data['create_date'] = $current_time;
		$data['update_date'] = $current_time;
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

    private static function orderUpBeforeAdd(IDependency $model, $product_category_id){

		$table = self::TABLE_NAME;

		$sql = "update {$table} set order_id = order_id + 1 where product_category_id = '{$product_category_id}'";
		return $model->executeSql($sql);

	}

    public static function toggleStatus(IDependency $model, $data)
    {

		if(empty($data['id']))
			throw new \Exception("Content::toggleStatus(".__LINE__.") id is null or empty.", 1);

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

    public static function deleteObj(IDependency $model, $id, $path_file)
	{
		//get object from id
		$product_obj = self::get($model, $id);
		if(empty($product_obj))
			return false;

		//delete self
		try{

			self::deteleFile($product_obj, $path_file);
			self::delete($model, ['id' => $product_obj['id']]);
		}catch (\Exception $e){
			// $response->error_msg = $e->getMessage();
            return false;
		}
		//return
		return true;

	}

    private static function deteleFile($product_obj, $path_file){

		if(empty($product_obj))
			return;

		// $src_file = $product_obj['file'];
		$src_thumb = $product_obj['thumb'];
		$src_pic = $product_obj['pic'];

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

    private static function delete(IDependency $model, $con)
	{
		return $model->delete($con);
	}

    public function deleteMany(IDependency $model, $product_id_arr)
	{
		if(empty($product_id_arr))
			return false;

		$table = self::TABLE_NAME;

		$sql = "delete from {$table} where id in ($product_id_arr)";

		$result = $model->executeSql($sql);
		return $result;
	}

}