<?php 

namespace Application\Module\CMS\Controller;

use Application\Module\CMS\Controller\BaseController;
use Application\Library\Proxy\Admin as AdminProx;
use Application\Util\AppException;

/**
* Home
*/
class Home extends BaseController
{

	public function index($params)
	{
		if(!AdminProx::isLogin()){
			throw new AppException("You have no authorize", 1, null);
		}

		$this->setAssetsFileForView($this->configValues['css']['Home']['index'], $this->configValues['js']['Home']['index']);
		return $this->view->render('Cms/Home/index.php', null);
	}

}