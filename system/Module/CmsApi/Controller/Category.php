<?php 

namespace Application\Module\CmsApi\Controller;

use Application\Module\CmsApi\Controller\BaseController;
use Application\Library\Proxy\Category as CategoryProx;
use Application\Helper\UrlHelper;

/**
* Category
*/
class Category extends BaseController
{

	public function getDataByCode($param)
	{
		$this->di->add("category", new \Application\Module\CmsApi\Model\Category());

		$category_code = $param['code'];
		return CategoryProx::getDataByCode($this->di->get("category"), $category_code);
	}

}