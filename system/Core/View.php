<?php 
namespace Application\Core;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_SimpleFilter;

/**
* View
*/
class View
{
	private $view;
	private $css;
	private $js;

	public function __construct()
	{
		$this->initViewObject();
	}

	private function initViewObject(){
		$loader = new Twig_Loader_Filesystem(GLOBAL_VIEW_PATH);
		$this->view = new Twig_Environment($loader, array(
		    'cache' => DATA_PATH . 'twig/cache',
		    'auto_reload' => true
		));
	}

	public function setCss($cssList){
		$this->css = $cssList;
	}
	public function setJs($jsList){
		$this->js = $jsList;
	}

	public function addFilter(Twig_SimpleFilter $filter)
	{
		if(empty($filter)){
			return;
		}

		$this->view->addFilter($filter);
	}

	public function render($file, $data)
	{
		$data['css'] = $this->css;
		$data['js'] = $this->js;
		return $this->view->render($file, $data);
	}

}