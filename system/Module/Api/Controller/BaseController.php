<?php

namespace Application\Module\Api\Controller;

use Application\Module\BaseController as AppController;

/**
* 
*/
class BaseController extends AppController
{
	public function __construct($configValues){
		parent::__construct($configValues);
	}
}