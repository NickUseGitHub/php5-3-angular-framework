<?php 

namespace Application\Module\ErrorPage;

use Application\Module\BaseModule;
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
	}

	public function dispatch($ErrorPage){

		//set header
		$headerOptions = array();
		$headerOptions['HTTP/1.1 404 page not found'] = "";
		HttpHeader::setHeader($headerOptions);

		echo "ErrorPage : {$ErrorPage}";
	}

}