<?php

namespace Application\Module\Cms\Controller;

use Application\Module\BaseController as AppController;

/**
* 
*/
class BaseController extends AppController
{
	
	protected $ip;

	public function __construct($configValues){
		parent::__construct($configValues);
		$this->setIp();
	}

	private function setIp()
	{
		$this->ip = $_SERVER['REMOTE_ADDR'];
	}

}