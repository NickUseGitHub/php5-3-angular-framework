<?php 

namespace Application\Module\CmsApi\Controller;

use Application\Module\CmsApi\Controller\BaseController;
use Application\Library\Proxy\Content as ContentProx;
use Application\Helper\UrlHelper;

/**
* Content
*/
class Content extends BaseController
{

	public function __construct($configValues){

		parent::__construct($configValues);
		$this->di->add("content", new \Application\Module\CmsApi\Model\Content());

	}

	public function addOrEdit($param)
	{
		$id = $param['id'];
		$param['author'] = $this->user['id'];
		$msg = "";
		if(empty($id)){
			$id = ContentProx::add($this->di->get("content"), $param);
			if(!empty($msg)){
				$msg = "ok";
			}else $msg = "nok";
		}else {
			$result = ContentProx::update($this->di->get("content"), $param);
			$msg = $result? "failed" : "success";
		}
		
		$response = array();
		$response['msg'] = "ok";
		return $response;
	}

	public function add($param)
	{
		$id = ContentProx::add($this->di->get("content"), $param);

		$response = new \stdClass();
		$response->id = $id;
		$response->msg = empty($id)? "failed" : "success";
		return $response;
	}

	public function delete($param)
	{
		$id = $param['id'];
		$result = ContentProx::delete($this->di->get("content"), ["id"=> $id]);

		$response = new \stdClass();
		$response->status = $result? "nok" : "ok";
		$response->msg = $result? "failed" : "success";
		return $response;
	}

	public function toggleStatus($param)
	{
		$id = $param['id'];
		$update_by = $this->user['id'];
		$result = ContentProx::toggleStatus($this->di->get("content"), ["id"=> $id, "update_by"=>$update_by]);

		$response = new \stdClass();
		$response->status = $result? "nok" : "ok";
		$response->msg = $result? "failed" : "success";
		return $response;
	}

	public function order($param)
	{
		$author = $this->user['id'];
		$list_for_order = $param['list_for_order'];

		$msg = ContentProx::updateMany($this->di->get("content"), $list_for_order, $author)? "ok" : "nok";
		
		$response = new \stdClass();
		$response->msg = $msg;
		$response->data = $list_for_order;
		return $response;
	}

	public function get($param)
	{
		$contentParams = $param;
		$list = ContentProx::get($this->di->get("content"), $contentParams);
		
		$response = array();
		$response['msg'] = "ok";
		$response['error_msg'] = "";
		$response['list'] = $list;
		$response['total_page'] = $contentParams['total_page'];

		return $response;
	}

	public function getData($param)
	{
		$id = $param['id'];
		return ContentProx::getData($this->di->get("content"), $id);
	}

}