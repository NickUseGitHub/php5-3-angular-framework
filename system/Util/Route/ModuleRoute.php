<?php

namespace Application\Util\Route;

use Application\Util\Route\RouteBase;

/**
* 
*/
class ModuleRoute extends RouteBase
{
	private $routes;
	protected static $moduleError = array(
										'module' => array(
											'moduleName' => 'ErrorPage',
											'module' => 'Application\\Module\\ErrorPage\\Module',
											'callback' => 'dispatch',
										)
									);

	function __construct($routeFile)
	{
		$this->initRoute($routeFile);
	}

	public function initRoute($routeFile)
	{
		if(!file_exists(CONFIG_PATH . $routeFile)){
			$this->routes = null;
			return;
		}

		$this->routes = include CONFIG_PATH . $routeFile;
	}

	public function getModule()
	{
		if(empty($this->routes) || !count($this->routes)){
			return null;
		}

		$uri = $this->getUri();

		//get home module
		$homeModule = array(
						"fullUri" => $uri, 
						"routeInfo" => null,
						'module' => array(
							'moduleName' => 'Home',
							'moduleNamespace' => 'Application\\Module\\Home',
							'module' => 'Application\\Module\\Home\\Module',
							'callback' => 'dispatch',
						),
					);

		if(strlen($uri) == 1 && $uri == "/" ){
			return $homeModule;
		}

		$routeInfo = null;
		foreach ($this->routes as $pattern => $module) {
			
			$routeInfo = $this->checkRouteURI($uri, $pattern);
			if(count($routeInfo)){
				return array(
							"fullUri" => $uri, 
							"routeInfo" => $routeInfo, 
							"module" => $module);
			}
		}
		
		return null;
	}

	public function getErrorPageModule()
	{
		return self::$moduleError;
	}

}