<?php 

namespace Application\Module\Test\Controller;

use Application\Module\Test\Controller\BaseController;
use Application\Library\Proxy\Admin as AdminProx;

class Test extends BaseController
{
    public function createAdmin($param){

		$this->di->add("admin", new \Application\Module\CmsApi\Model\Admin());
		
		$data = array();
		$data['username'] = "admin";
		$data['name'] = "Super Cellox Admin";
		$data['password'] = "1234";
		$data['status'] = 1;
		$data['type'] = AdminProx::getTypeSuperAdmin();
		$admin_id = AdminProx::addAdmin($this->di->get("admin"), $data);

		die("admin {$admin_id}");

	}

    public function index()
    {
        return $this->view->render('Test/index.php', array('name' => 'Nick'));
    }
}