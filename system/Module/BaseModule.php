<?php

namespace Application\Module;

use Application\Module\ModuleInterface;
use Application\Util\Route\ControllerRoute;
use Herrera\FileLocator\Locator\FileSystemLocator;
use Symfony\Component\Yaml\Yaml;
use Application\Util\AppException;

class BaseModule implements ModuleInterface
{

	protected $uriInfo;
	protected $moduleConfig;
	protected $configValues;
	protected $controllerRoutes;
	protected $controllerHandler;
	protected $controller;

	public function __construct($uriInfo, $strModule)
	{
		$this->defineModuleDir($strModule);

		$configFiles = $this->getConfigFiles();
		$this->configValues = $this->initValueInConfigFile($configFiles);

		$this->uriInfo = $this->initRouteInfo($uriInfo);

		$routes = $this->configValues['routes'];
		$this->controllerRoutes = new ControllerRoute($routes);
	}

	public function __destruct(){}

	protected function defineModuleDir($strModule)
	{
		if (!defined('MODULE_PATH')) {
			define('MODULE_PATH', SYSTEM_PATH."Module/{$strModule}/");
		}
	}

	protected function initRouteInfo($routeInfo) {
    	
    	$fullUri = $routeInfo['fullUri'];
    	$search = $routeInfo['routeInfo']['1'];
    	$replace = "";
    	$uri = str_replace($search, $replace, $fullUri);
    	$uri = empty($uri)? "/" : $uri;

    	unset($routeInfo['routeInfo']);
    	unset($routeInfo['fullUri']);
    	$routeInfo['routeInfo']['controllerUri'] = $uri;
    	$routeInfo['routeInfo']['fullUri'] = $fullUri;
        $routeInfo['routeInfo']['module'] = $routeInfo['module']['module'];

    	return $routeInfo;

    }

    protected function getParamsFromRoute($routeInfo){

        //get param from route
        $params = array_filter($routeInfo, function($v) { 
            return $v !== ''; 
        });

        foreach ($params as $key => $value) {
            $temp_str = str_replace("p_", "", $key);
            $params[$temp_str] = array_filter( explode("/", $value), function($v) { 
                                    return $v !== ''; 
                                });
            $params[$temp_str] = array_shift($params[$temp_str]);
            unset($params[$key]);
        }

        return $params;
    }

    protected function getParamsFromHttpMethods()
    {
        return array_merge($_GET, $_POST);
    }

    protected function getParamsFromAngularJson()
    {
    	return (array) json_decode(file_get_contents('php://input'), true);
    }

	private function getConfigFiles()
	{
		$configDirectories = MODULE_PATH.'Config';
		$locator = new FileSystemLocator($configDirectories);
		
		$configFileList = glob($configDirectories . "/*.*");
		unset($locator);
		return $configFileList;
	}

	private function initValueInConfigFile($configFiles)
	{
		if(empty($configFiles)){
			return null;
		}

		$temp = array();
		foreach ($configFiles as $key => $file) {
			$config = Yaml::parse($file);
			$temp = array_merge($temp, $config);
		}

		return $temp;
	}

	public function loadController($controllerHandler, $configValues)
    {
        if(empty($controllerHandler)){
            $options = array(
                            'headerOptions' => array("HTTP/1.1 404" => "")
                        );
            throw new AppException("Error Controller handler", 1, $options);
        }

        $moduleNamespace = $this->uriInfo['module']['moduleNamespace'];
        $className = $moduleNamespace . "\\Controller\\" . $controllerHandler['controller'];
        
        if(!class_exists($className)){
            $options = array(
                            'headerOptions' => array("HTTP/1.1 404" => "")
                        );
            throw new AppException("Error Controller Not exist", 1);
        }

        return new $className($configValues);
    }

	public function dispatch(){

		$httpMethod = $this->controllerRoutes->getHttpRequestMethod();
		$controllerUri = $this->uriInfo['routeInfo']['controllerUri'];
		$this->controllerHandler = $this->controllerRoutes->getControllerWithHttpMethod($controllerUri, $httpMethod);
		$this->controller = $this->loadController($this->controllerHandler, $this->configValues);

	}

}