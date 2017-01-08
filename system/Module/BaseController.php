<?php

namespace Application\Module;

use Application\Core\View;
use Application\Library\Dependency\Di;
use Twig_SimpleFilter;

class BaseController
{
	
	protected $configValues;
	protected $view;
	protected $di;

	public function __construct($configValues){
		$this->configValues = $configValues;
		$this->configView();
		$this->setFilterOnView();
		$this->initDependency();
	}

	public function __destruct(){
		unset($this->configValues);
		unset($this->view);
		unset($this->di);
	}

	private function setFilterOnView()
	{
		$this->view->addFilter(new Twig_SimpleFilter('site_url', array('Application\\Helper\\UrlHelper', 'site_url')));
	}

	private function initDependency(){
		$this->di = new Di();
	}

	protected function configView(){
		$this->view = new View();
	}

    protected function setAssetsFileForView($assetCssArr, $assetJsArr)
	{
		$this->view->setCss($assetCssArr);
		$this->view->setJs($assetJsArr);
	}

}