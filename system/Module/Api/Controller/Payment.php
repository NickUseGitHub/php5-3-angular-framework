<?php 

namespace Application\Module\Api\Controller;

use Application\Module\Api\Controller\BaseController;
use Application\Library\Proxy\Bill as BillProxy;
use Application\Library\Proxy\Client as ClientProxy;

/**
* Payment
*/
class Payment extends BaseController
{

    public function confirm($params)
    {
        $paymentFields = $params['field'];
        $data = array();
        $data['code'] = $paymentFields['code'];
        $data['slip'] = $paymentFields['slip'];
        
        $this->di->add("billOrderModel", new \Application\Module\Cms\Model\Bill());
        $this->di->add("clientOrderModel", new \Application\Module\CmsApi\Model\Client());

        $response = array();
        $userObj = ClientProxy::getUserObjByEmail($this->di->get("clientOrderModel"), $paymentFields['email']);
        if (empty($userObj)) {
            $response['msg'] = "This email is not valid.";
            $response['status'] = 0;
            return $response;
        }

        $data['paid_status'] = BillProxy::PAID_STATUS_PAID;
        $billObj = BillProxy::getBillByCode($this->di->get("billOrderModel"), $data['code']);
        if (empty($billObj) || $billObj['paid_status'] == BillProxy::PAID_STATUS_PAID) {
            $response['msg'] = "This code is not valid.";
            $response['status'] = 0;
            return $response;
        }

        $result = BillProxy::update($this->di->get("billOrderModel"), 'code', $data, $userObj['id']);
        $response['msg'] = $result? "ok" : "nok";
        $response['status'] = $result? 1 : 0;
        return $response;
    }

}