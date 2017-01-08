<?php 

namespace Application\Module\CMS\Controller;

use Application\Module\CMS\Controller\BaseController;
use Application\Library\Proxy\Admin as AdminProx;
use Application\Util\HttpHeader;
use Application\Util\Session;

/**
* Login
*/
class Login extends BaseController
{

	public function index($params)
	{
		$this->setAssetsFileForView($this->configValues['css']['Login']['index'], $this->configValues['js']['Login']['index']);
		return $this->view->render('Cms/Login/index.php', null);
	}

	public function login($param){
		
		$this->di->add("admin", new \Application\Module\Cms\Model\Admin());
		$this->di->add("token", new \Application\Module\Cms\Model\Token());

		$username = $param['username'];
		$password = $param['password'];
		$ip = $this->ip;

		$token = AdminProx::login($this->di->get("admin"), $this->di->get("token"), $username, $password, $ip);

		if(!empty($token)){
			// Session::set("admin_token", $token);
			session_start();
			$_SESSION["admin_token"] = $token;
		}

		//set header
		$headerOptions = array();
		$headerOptions['HTTP/1.1 200'] = "";
		$headerOptions['Content-Type'] = "application/json";
		HttpHeader::setHeader($headerOptions);

		$responseMsg = array();
		$responseMsg['msg'] = empty($token)? "nok" : "ok";
		return json_encode($responseMsg);

	}

}