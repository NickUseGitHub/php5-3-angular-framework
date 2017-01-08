<?php 

namespace Application\Module\CmsApi\Controller;

use Application\Module\CmsApi\Controller\BaseController;
use Application\Library\Dependency\IDependency;
use Application\Library\Proxy\Product as ProductProx;
use Application\Library\Proxy\ProductCategory as ProductCategoryProx;

/**
* Product
*/
class Product extends BaseController
{

    public function get($params)
	{
		$id = $params['id'];
        $this->di->add("productModel", new \Application\Module\CmsApi\Model\Product());
		return ProductProx::get($this->di->get("productModel"), $id);
	}

    public function getList($params){
		
		$response = array();

		$product_category_id = $params['product_category_id'];
		if(empty($product_category_id)){
			$response['msg'] = "nok";
			$response['error_msg'] = "product_category_id is null or empty.";
			$response['list'] = null;
			$response['total_page'] = null;

			return $response;
		}

		$params['page'] = empty($param) || empty($params['page'])? 1 : $params['page'];
		$params['amount_per_page'] = empty($param) || empty($params['amount_per_page'])? 10 : $params['amount_per_page'];

        $this->di->add("productModel", new \Application\Module\CmsApi\Model\Product());
		$list = ProductProx::getList($this->di->get("productModel"), $params);
		
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
        $this->di->add("productModel", new \Application\Module\CmsApi\Model\Product());

		try{
			$author = $this->user['id'];
			$id = ProductProx::createOrUpdate($this->di->get("productModel"), $data, $author);
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
        $this->di->add("productModel", new \Application\Module\CmsApi\Model\Product());
		$result = ProductProx::toggleStatus($this->di->get("productModel"), ["id"=> $id, "update_by"=>$update_by]);

		$response = array();
		$response['status'] = $result? "nok" : "ok";
		$response['msg'] = $result? "failed" : "success";
		return $response;

	}

    public function order($params){
		
		$author = $this->user['id'];
		$list_for_order = $params['list_for_order'];

        $this->di->add("productModel", new \Application\Module\CmsApi\Model\Product());
		$msg = ProductProx::updateMany($this->di->get("productModel"), $list_for_order, $author)? "ok" : "nok";
		
		$response = array();
		$response['msg'] = $msg;
		$response['data'] = $list_for_order;
		return $response;

	}

    public function delete($params){

		$id = $params['id'];
		$path_file = $params['path_file'];

        $this->di->add("productModel", new \Application\Module\CmsApi\Model\Product());
		ProductProx::deleteObj($this->di->get("productModel"), $id, $path_file);

		$response = array();
		$response['msg'] = "ok";
		$response['error_msg'] = "";
		return $response;

	}

    public function deleteProductByProductCategoryId($params)
	{
		$product_category_id = $params['product_category_id'];
		$path_file = "product";

        $this->di->add("productModel", new \Application\Module\CmsApi\Model\Product());
        $this->di->add("productCategoryModel", new \Application\Module\CmsApi\Model\ProductCategory());

        $productModel = $this->di->get("productModel");
        $productCategoryModel = $this->di->get("productCategoryModel");
		$response = array();

		try{

			$this->deleteProductIterative($productModel, $productCategoryModel, $product_category_id, $path_file);
			$response['msg'] = "ok";
			$response['error_msg'] = "";
		}catch (\Exception $e){
			$response['msg'] = "nok";
			$response['error_msg'] = $e->getMessage();
		}

		return $response;

	}

    private function deleteProductIterative(IDependency $productModel, IDependency $productCategoryModel, $product_category_id, $path_file){

		if(empty($product_category_id))
			return;

		//get object from product_category_id
		$product_category_obj = ProductCategoryProx::get($productCategoryModel, $product_category_id);
		if(empty($product_category_obj))
			return;

		//get children
		$product_category_children_list = ProductCategoryProx::getChildren($productCategoryModel, $product_category_id);

		//delete children iteration
		if(!empty($product_category_children_list)){
			foreach ($product_category_children_list as $key => $product_category_children_obj) {
				
				$children_id = $product_category_children_obj['id'];
				$this->deleteProductIterative($productModel, $productCategoryModel, $children_id, $path_file);
			}
		}

		//delete self
		try{

			$product_id_arr = $this->deleteGroupFile($productModel, $product_category_id, $path_file);
			ProductProx::deleteMany($productModel, $product_id_arr);
		}catch (\Exception $e){
			// $response->error_msg = $e->getMessage();
		}
		//return
		return;

	}

    private function deleteGroupFile(IDependency $productModel, $product_category_id, $path_file)
	{
		//get all products list
		$product_obj_list = ProductProx::getDataListByProductCategoryId($productModel, $product_category_id);

		if(empty($product_obj_list))
			return null;

		$product_id_arr = array();

		foreach ($product_obj_list as $key => $product_obj){
			$this->deteleFile($product_obj, $path_file);
			$product_id_arr[] = $product_obj['id'];
		}

		$product_id_arr = implode(',', $product_id_arr);

		return $product_id_arr;
	}

	private function deteleFile($product_obj, $path_file){

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

}