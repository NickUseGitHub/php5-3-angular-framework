<?php 

namespace Application\Module\Test;

use Application\Module\BaseModule;
use Application\Module\InitController;
use Application\Util\HttpHeader;

/**
* 
*/
class Module extends BaseModule implements InitController
{

	public function __construct($uriInfo, $moduleName)
	{
		parent::__construct($uriInfo, $moduleName);
	}

	public function __destruct(){
		unset($this->controller);
	}

	public function dispatch(){

		$httpMethod = $this->controllerRoutes->getHttpRequestMethod();
		$controllerUri = $this->uriInfo['routeInfo']['controllerUri'];
		$controllerHandler = $this->controllerRoutes->getControllerWithHttpMethod($controllerUri, $httpMethod);
		$this->controller = $this->loadController($controllerHandler, $this->configValues);

		$params = array();
		$action = $controllerHandler['action'];
		$params = $this->getParamsFromRoute($controllerHandler['routeInfo']);
		$params = array_merge ( $params, $this->getParamsFromHttpMethods());

		//set header
		$headerOptions = array();
		$headerOptions['HTTP/1.1 200'] = "";
		$headerOptions['Content-Type'] = "text/html; charset=utf-8";
		HttpHeader::setHeader($headerOptions);

		echo $this->controller->{"{$action}"}($params);
	}

}