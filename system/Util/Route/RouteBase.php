<?php

namespace Application\Util\Route;

use Application\Util\Regex;

/**
* 
*/
abstract class RouteBase
{
	
	private $route;
	
	abstract public function initRoute($pathFile);

	public function getUri()
	{
		// Fetch method and URI from somewhere
		$httpMethod = $_SERVER['REQUEST_METHOD'];
		$uri = $_SERVER['REQUEST_URI'];

		// Strip query string (?foo=bar) and decode URI
		if (false !== $pos = strpos($uri, '?')) {
		    $uri = substr($uri, 0, $pos);
		}
		$uri = rawurldecode($uri);

		return $uri;
	}

	public function getHttpRequestMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	public function checkRouteURI($uri, $patern)
	{
		if(empty($uri)){
			return null;
		}
		if(empty($patern)){
			return null;
		}

		$routeInfos = Regex::match($uri, $patern);

		return $routeInfos;

	}

}