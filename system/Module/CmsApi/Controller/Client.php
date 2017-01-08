<?php 

namespace Application\Module\CmsApi\Controller;

use Application\Module\CmsApi\Controller\BaseController;
use Application\Library\Proxy\Client as ClientProx;

/**
* Client
*/
class Client extends BaseController
{
    public function get($params)
    {
        $this->di->add("clientModel", new \Application\Module\CmsApi\Model\Client());
        $list = ClientProx::get($this->di->get("clientModel"), $params);

        $response = array();
		$response['msg'] = "ok";
		$response['error_msg'] = "";
		$response['list'] = $list;
		$response['total_page'] = $params['total_page'];

		return $response;
    }

    public function getDetail($params)
    {
        $this->di->add("clientModel", new \Application\Module\CmsApi\Model\Client());
        $user_detail = ClientProx::getUser($this->di->get("clientModel"), $params['member_id']);

        $response = array();
        $response['status'] = 1;
        $response['msg'] = "success";
        $response['user_detail'] = $user_detail;
        return $response;
    }

}