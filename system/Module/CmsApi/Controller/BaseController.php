<?php

namespace Application\Module\CmsApi\Controller;

use Application\Module\BaseController as AppController;
use Application\Library\Proxy\Admin as AdminProx;

/**
* 
*/
class BaseController extends AppController
{
	
	protected $token;
	protected $user;

	public function __construct($configValues){
		parent::__construct($configValues);
		$this->setTokenFromSession();
		$this->addUserDependency();
		$this->setUser();
	}

	public function __destruct(){
		unset($this->ip);
		unset($this->configValues);
		unset($this->di);
	}

	private function setIp()
	{
		$this->ip = $_SERVER['REMOTE_ADDR'];
	}

	private function setTokenFromSession()
	{
		$this->token = AdminProx::getTokenFromSession();
	}

	private function setUser()
	{
		$this->user = AdminProx::getUserFromToken($this->di->get("admin"), $this->token);
	}

	private function addUserDependency()
	{
		$this->di->add("admin", new \Application\Module\Cms\Model\Admin());
	}

}