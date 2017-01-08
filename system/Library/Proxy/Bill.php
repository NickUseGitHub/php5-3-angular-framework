<?php

namespace Application\Library\Proxy;

use Application\Library\Dependency\IDependency;

/**
* 
*/
class Bill
{
	const TABLE_BILL_NAME = "bill_order";
	const TABLE_BILLITEM_NAME = "bill_order_item";

	const PAID_STATUS_NOT_PAID = 0;
	const PAID_STATUS_PAID = 1;

	const SHIPPING_STATUS_NOT_SHIP = 0;
	const SHIPPING_STATUS_NOW_SHIPPING = 1;
	const SHIPPING_STATUS_SHIPPED = 2;

	public static function getBills(IDependency $model, &$paramCond)
	{
		$search = $paramCond['search'];
		$page = $paramCond['page']? : 1;
		$amount_per_page = $paramCond['amount_per_page'];

		$where_kewword = empty($search)? "" : " AND code like '%{$search}%'";

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

	public function getItems(IDependency $model, $bill_order_id)
	{
		if (empty($bill_order_id)) {
			return null;
		}

		$table = self::TABLE_BILLITEM_NAME;

		$sql = "select *
				from {$table}
				where 
					bill_order_id = '{$bill_order_id}'
				order by create_date desc";

		$result = $model->getRows($sql);
		
		return $result;
	}

	public static function toggleStatus(IDependency $model, $table, $schema_name, $id, $update_by)
	{
		$current_time = date('Y-m-d H:i:s');
		$sql = "update {$table} 
					set 
							{$schema_name} = CASE {$schema_name} 
							  WHEN 0 THEN 1
							  WHEN 1 THEN 0
							END
						,	update_date = '{$current_time}'
						,	update_by = '{$update_by}'
				where id = '{$id}'";

		return !$model->executeSql($sql);
	}

	public static function setStatus(IDependency $model, $table, $schema_name, $id, $status, $update_by)
	{
		$current_time = date('Y-m-d H:i:s');
		$sql = "update {$table} 
					set 
							{$schema_name} = '{$status}'
						,	update_date = '{$current_time}'
						,	update_by = '{$update_by}'
				where id = '{$id}'";

		return !$model->executeSql($sql);
	}

    public static function checkout(IDependency $modelBill, IDependency $modelBillOrderItem, $items, $client_detail, $client_id)
    {
        $billData = $client_detail;
        $billData['code'] = "something";
        
        //TODO:: Calculate the price
        $billData['total_price'] = 3230;
        $billData['shipping_status'] = $billData['paid_status'] = 0;

        $bill_order_id = self::add($modelBill, $billData, $client_id);

		self::addItemList($modelBillOrderItem, $items, $bill_order_id, $client_id);
		return $bill_order_id;
    }

	public static function addItemList(IDependency $model, $items, $bill_order_id, $client_id)
	{
		$current_time = date('Y-m-d H:i:s');
		
		$datas = array_map(function($item) use ($bill_order_id) {
			
			$code = $item['id'];
			$amount = $item['amount'];

			return array_map(function($partial) use ($code, $amount, $current_time, $bill_order_id, $client_id) {

				$partial['code'] = $code;
				$partial['bill_order_id'] = $bill_order_id;
				$partial['pic'] = $partial['file_name'];
				$partial['amount'] = $amount;
				$partial['price_per_piece'] = 200;
				$partial['freight_price'] = 50;
				unset($partial['partialName']);
				unset($partial['file_name']);

				return $partial;
			}, $item['partials']);
		}, $items);
		
		$item_for_add = array();
		foreach ($datas as $data_items) {
			foreach ($data_items as $item) {
				$item['create_by'] = $client_id;
				$item['update_by'] = $client_id;
				$item['create_date'] = $current_time;
				$item['update_date'] = $current_time;

				$item_for_add[] = $item;
			}
		}

		return $model->insertMany($item_for_add);
	}

    private static function add(IDependency $model, $data, $client_id)
	{
		if(empty($model)){
			return null;
		}

		$current_time = date('Y-m-d H:i:s');

		$data['create_by'] = $client_id;
		$data['update_by'] = $client_id;
		$data['create_date'] = $current_time;
		$data['update_date'] = $current_time;
		$id = $model->insert($data);

		return $id;
	}

    public static function update(IDependency $model, $keyArr, $data, $author)
	{
		if(empty($model)){
			return false;
		}

		$current_time = date('Y-m-d H:i:s');

		$data['update_by'] = $author;
		$data['update_date'] = $current_time;
		return $model->update([ $keyArr => $data[$keyArr] ], $data);
	}

	public static function getBillByCode(IDependency $model, $code)
	{
		if(empty($code)){
			return null;
		}

		$table = self::TABLE_BILL_NAME;
		$sql = "select * from {$table} where code = '{$code}'";
		$result = $model->getRows($sql);
		return empty($result) || empty($result[0])? null : $result[0];
	}
}