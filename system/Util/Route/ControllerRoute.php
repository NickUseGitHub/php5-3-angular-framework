<?php

namespace Application\Util\Route;

use Application\Util\Route\RouteBase;

/**
* 
*/
class ControllerRoute extends RouteBase
{
	private $routes;

	function __construct($routes)
	{
		$this->initRoute($routes);
	}

	public function initRoute($routes)
	{
		$this->routes = $routes;
	}

	public function getController($uri)
	{
		if(empty($this->routes) || !count($this->routes)){
			return null;
		}

		//get indexController
		$controller = array(
							'controller' => 'Index',
							'action' => 'index',
						);
		if(strlen($uri) == 1 && $uri == "/" ){
			return $controller;
		}

		$routeInfo = null;
		foreach ($this->routes as $pattern => $controllerHandler) {
			
			$routeInfo = $this->checkRouteURI($uri, $pattern);

			if(count($routeInfo)){
				if(array_key_exists('action', $routeInfo)){
					$controllerHandler['action'] = $routeInfo['action'];			
				}

				return $controllerHandler;
			}
		}

		return null;
	}

	public function getControllerWithHttpMethod($uri, $httpMethod)
	{
		if(empty($httpMethod)){
			return null;
		}
		if(!count($this->routes) || !count($this->routes[$httpMethod])){
			return null;
		}

		$routeInfo = null;
		foreach ($this->routes[$httpMethod] as $pattern => $controllerHandler) {
			
			$routeInfo = $this->checkRouteURI($uri, $pattern);

			if(count($routeInfo)){
				if(array_key_exists('action', $routeInfo)){
					$controllerHandler['action'] = $routeInfo['action'];			
				}

				$controllerHandler['routeInfo'] = $routeInfo;
				return $controllerHandler;
			}
		}

		return null;
	}

}