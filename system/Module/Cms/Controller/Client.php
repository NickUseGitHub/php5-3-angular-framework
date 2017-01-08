<?php 

namespace Application\Module\CMS\Controller;

use Application\Module\CMS\Controller\BaseController;
use Application\Library\Proxy\Client as ClientProx;
use Application\Util\AppException;

/**
* Client
*/
class Client extends BaseController
{
    public function add($params)
    {
        $this->di->add("clientModel", new \Application\Module\CmsApi\Model\Client());
        
        $data = array();
        $data['point'] = '300';
        $data['username'] = 'username';
        $data['password'] = 'password';
        $data['paid_title'] = 'paid_title';
        $data['paid_name'] = 'paid_name';
        $data['paid_lastname'] = 'paid_lastname';
        $data['paid_email'] = 'paid_email';
        $data['paid_address'] = 'paid_address';
        $data['paid_city'] = 'paid_city';
        $data['paid_postal_code'] = 'paid_postal_code';
        $data['paid_country'] = 'paid_country';
        $data['paid_tel'] = 'paid_tel';
        $data['paid_fax'] = 'paid_fax';
        $data['paid_mobile'] = 'paid_mobile';
        $data['shipping_title'] = 'shipping_title';
        $data['shipping_name'] = 'shipping_name';
        $data['shipping_lastname'] = 'shipping_lastname';
        $data['shipping_email'] = 'shipping_email';
        $data['shipping_address'] = 'shipping_address';
        $data['shipping_city'] = 'shipping_city';
        $data['shipping_postal_code'] = 'shipping_postal_code';
        $data['shipping_country'] = 'shipping_country';
        $data['shipping_tel'] = 'shipping_tel';
        $data['shipping_fax'] = 'shipping_fax';
        $data['shipping_mobile'] = 'shipping_mobile';
        $data['status'] = '0';

        $id = ClientProx::addClient($this->di->get("clientModel"), $data, null);
        return "hello: {$id}";
    }

}