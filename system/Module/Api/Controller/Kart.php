<?php 

namespace Application\Module\Api\Controller;

use Application\Module\Api\Controller\BaseController;
use Application\Library\Proxy\Bill as BillProx;
use Application\Library\Proxy\Client as ClientProx;
use Application\Helper\UrlHelper;
use Application\Library\ImageRender;

/**
* Kart
*/
class Kart extends BaseController
{
    public function purchase($params)
    {
        $response = new \stdClass();

        $client_detail = $params['customer']['detail'];
        $this->di->add("clientModel", new \Application\Module\CmsApi\Model\Client());
        $client_id = ClientProx::register($this->di->get("clientModel"), $client_detail, null);

        if (empty($client_id)) {
            $response->status = 0;
            $response->msg = "cannot register client";
            return $response;
        }

        //TODO:: Add bill
        $items = $params['items'];
        $this->di->add("billOrderModel", new \Application\Module\Cms\Model\Bill());
        $this->di->add("billItemsModel", new \Application\Module\Cms\Model\BillItem());
        $bill_order_id = BillProx::checkout($this->di->get("billOrderModel"), $this->di->get("billItemsModel"), $items, $client_detail, $client_id);

        $response->status = 1;
        $response->msg = "success";
        return $response;
    }
}