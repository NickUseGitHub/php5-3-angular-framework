<?php 

namespace Application\Module\CMS;

use Application\Module\BaseModule;
use Application\Module\InitController;
use Application\Util\HttpHeader;

/**
* 
*/
class Module extends BaseModule
{

	public function __construct($uriInfo, $moduleName)
	{
		parent::__construct($uriInfo, $moduleName);
	}

	public function __destruct(){
		unset($this->controller);
	}

	public function dispatch(){

		parent::dispatch();

		$params = array();
		$action = $this->controllerHandler['action'];
		$params = $this->getParamsFromRoute($this->controllerHandler['routeInfo']);
		$params = array_merge($params, $this->getParamsFromAngularJson());
		$params = array_merge ( $params, $this->getParamsFromHttpMethods());

		//set header
		$headerOptions = array();
		$headerOptions['HTTP/1.1 200'] = "";
		$headerOptions['Content-Type'] = "text/html; charset=utf-8";
		HttpHeader::setHeader($headerOptions);

		echo $this->controller->{"{$action}"}($params);
	}

}