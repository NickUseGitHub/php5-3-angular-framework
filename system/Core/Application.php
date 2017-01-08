<?php 
namespace Application\Core;

use Application\Module\ModuleInterface;
use Application\Util\Route\ModuleRoute;
use Application\Util\HttpHeader;
use Application\Util\AppException;

/**
* Application class
*/
class Application
{

	private $route;
	private static $app;

	private function __construct(){
		$this->route = new ModuleRoute("routes.php");
	}

	private static function getApp()
	{
		if (self::$app == null) {
			self::$app = new static;
		}
		return self::$app;
	}

	public function loadModule($routeInfo)
	{
		$moduleName = $routeInfo['module']['moduleName'];
		$module = $routeInfo['module']['module'];
		return new $module($routeInfo, $moduleName);
	}

	public static function dispatch()
	{
		$app = self::getApp();
		$routeInfo = $app->route->getModule();

		try{

			if(empty($routeInfo)){
				throw new AppException("404 not found", 1);
			}

			$module = $app->loadModule($routeInfo);
			$callback = $routeInfo['module']['callback'];
			$module->{"{$callback}"}();

		}catch(AppException $e){

			$routeInfo = $app->route->getErrorPageModule();
			$module = $app->loadModule($routeInfo);
			$callback = $routeInfo['module']['callback'];

			$options = $e->GetOptions();
			$headerOptions = empty($options['headerOptions'])? array("HTTP/1.1 404 Not Found" => "") : $options['headerOptions'];
			HttpHeader::setHeader($options['headerOptions']);
			$module->{"{$callback}"}($e->getMessage());

		}
		unset($module);
	}

}