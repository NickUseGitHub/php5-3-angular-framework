<?php
namespace Application\Helper;

/**
* UrlHelper
*/
class UrlHelper
{
	
	public function site_url($path_url)
	{
		$str_http = isset($_SERVER['HTTPS'])? "https://" : "http://";
		return $str_http . BASE_URL . "/" . $path_url;
	}

	public static function static_site_url($path_url)
	{
		$str_http = isset($_SERVER['HTTPS'])? "https://" : "http://";
		return $str_http . BASE_URL . "/" . $path_url;
	}

}