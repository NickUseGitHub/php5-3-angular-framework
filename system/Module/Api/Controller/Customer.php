<?php 

namespace Application\Module\Api\Controller;

use Application\Module\Api\Controller\BaseController;
use Application\Library\Proxy\Client as ClientProx;

/**
* Customer
*/
class Customer extends BaseController
{
    public function getByEmail($params)
    {
        $email = $params['email'];
        if (empty($email)) {
            $response->status = 0;
            $response->msg = "error: email is null or empty.";
            return $response;
        }

        $this->di->add("clientModel", new \Application\Module\CmsApi\Model\Client());
        $user_detail = ClientProx::getUserObjByEmail($this->di->get("clientModel"), $email);

        $user_detail['paid_postal_code'] = intval($user_detail['paid_postal_code']);
        $user_detail['paid_tel'] = intval($user_detail['paid_tel']);
        $user_detail['paid_fax'] = intval($user_detail['paid_fax']);
        $user_detail['paid_mobile'] = intval($user_detail['paid_mobile']);

        $user_detail['shipping_postal_code'] = intval($user_detail['shipping_postal_code']);
        $user_detail['shipping_tel'] = intval($user_detail['shipping_tel']);
        $user_detail['shipping_fax'] = intval($user_detail['shipping_fax']);
        $user_detail['shipping_mobile'] = intval($user_detail['shipping_mobile']);

        $response = new \stdClass();
        $response->status = 1;
        $response->msg = "success";
        $response->user_detail = $user_detail;
        return $response;
    }
}