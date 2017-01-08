<?php 

namespace Application\Module\CmsApi\Controller;

use Application\Module\CmsApi\Controller\BaseController;
use Application\Library\Proxy\ProductCategory as ProductCategoryProx;

/**
* ProductCategory
*/
class ProductCategory extends BaseController
{
	public function get($params)
	{
		$product_category_id = $params['id'];
        $this->di->add("productCategoryModel", new \Application\Module\CmsApi\Model\ProductCategory());
		$data = ProductCategoryProx::get($this->di->get("productCategoryModel"), $product_category_id);

		return $data;
	}

	public function getList($params){
		
		$params['page'] = 1;
		$params['amount_per_page'] = 30;
        $this->di->add("productCategoryModel", new \Application\Module\CmsApi\Model\ProductCategory());
		$list = ProductCategoryProx::getList($this->di->get("productCategoryModel"), $params);
		
		$response = array();
		$response['msg'] = "ok";
		$response['error_msg'] = "";
		$response['list'] = $list;
		$response['total_page'] = $params['total_page'];

		return $response;

	}

	public function addOrEdit($params){
		
		$data = $params['field'];

		$id = null;
		$error_msg = "";
        $this->di->add("productCategoryModel", new \Application\Module\CmsApi\Model\ProductCategory());

		try{
			$author = $this->user['id'];
			$id = ProductCategoryProx::createOrUpdate($this->di->get("productCategoryModel"), $data, $author);
			$error_msg = "";
		}catch (\Exception $e){
			$id = null;
			$error_msg = $e->getMessage();
		}

		$response = array();
		$response['msg'] = empty($id)? "nok" : "ok";
		$response['error_msg'] = $error_msg;

		return $response;

	}

	public function toggleStatus($params){
		
		$id = $params['id'];
		$update_by = $this->user['id'];
        $this->di->add("productCategoryModel", new \Application\Module\CmsApi\Model\ProductCategory());
		$result = ProductCategoryProx::toggleStatus($this->di->get("productCategoryModel"), ["id"=> $id, "update_by"=>$update_by]);

		$response = array();
		$response['status'] = $result? "nok" : "ok";
		$response['msg'] = $result? "failed" : "success";
		return $response;

	}

    public function order($params)
	{
		$author = $this->user['id'];
		$list_for_order = $params['list_for_order'];
        $this->di->add("productCategoryModel", new \Application\Module\CmsApi\Model\ProductCategory());
		$msg = ProductCategoryProx::updateMany($this->di->get("productCategoryModel"), $list_for_order, $author)? "ok" : "nok";
		
		$response = array();
		$response['msg'] = $msg;
		$response['data'] = $list_for_order;
		return $response;
	}

	public function delete($params){

		$id = $params['id'];
		$path_file = $params['path_file'];

        $this->di->add("productCategoryModel", new \Application\Module\CmsApi\Model\ProductCategory());
		ProductCategoryProx::deleteObj($this->di->get("productCategoryModel"), $id, $path_file);

		$response = array();
		$response['msg'] = "ok";
		$response['error_msg'] = "";
		return $response;

	}

}