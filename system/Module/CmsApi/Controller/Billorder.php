<?php 

namespace Application\Module\CmsApi\Controller;

use Application\Module\CmsApi\Controller\BaseController;
use Application\Library\Proxy\Bill as BillProx;

/**
* Billorder
*/
class Billorder extends BaseController
{

	public function get($param)
	{
        $this->di->add("billOrderModel", new \Application\Module\Cms\Model\Bill());
		$list = BillProx::getBills($this->di->get("billOrderModel"), $param);
		
		$response = array();
		$response['msg'] = "ok";
		$response['error_msg'] = "";
		$response['list'] = $list;
		$response['total_page'] = $param['total_page'];

		return $response;
	}

	public function getItems($param)
	{
		$this->di->add("billItemsModel", new \Application\Module\Cms\Model\BillItem());
		$list = BillProx::getItems($this->di->get("billItemsModel"), $param['bill_id']);

		$response = array();
		$response['msg'] = "ok";
		$response['error_msg'] = "";
		$response['list'] = $list;

		return $response;
	}

	public function togglePaidStatus($param)
	{
		$id = $param['id'];
		$table = BillProx::TABLE_BILL_NAME;
		$schema_name = "paid_status";
		$update_by = $this->user['id'];
		$this->di->add("billOrderModel", new \Application\Module\Cms\Model\Bill());
		$result = BillProx::toggleStatus($this->di->get("billOrderModel"), $table, $schema_name, $id, $update_by);
		
		$response = new \stdClass();
		$response->status = $result? "nok" : "ok";
		$response->msg = $result? "failed" : "success";
		return $response;
	}

	public function setShippingStatus($param)
	{
		$id = $param['id'];
		$shipping_status = $param['shipping_status'];
		$table = BillProx::TABLE_BILL_NAME;
		$schema_name = "shipping_status";
		$update_by = $this->user['id'];
		$this->di->add("billOrderModel", new \Application\Module\Cms\Model\Bill());
		$result = BillProx::setStatus($this->di->get("billOrderModel"), $table, $schema_name, $id, $shipping_status, $update_by);
		
		$response = new \stdClass();
		$response->status = $result? "nok" : "ok";
		$response->msg = $result? "failed" : "success";
		return $response;
	}

}