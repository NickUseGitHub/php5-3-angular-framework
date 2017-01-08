<?php

namespace Application\Library\Dependency;

use Application\Library\Dependency\IDependency;

/**
* 
*/
class Di
{
	private $di;

	public function __construct(){
		$this->di = array();
	}
	public function __destruct(){
		foreach ($this->di as $key => $dependency) {
			unset($dependency);
		}
	}

	public function add($name, IDependency $dependency)
	{
		if(empty($name)){
			return;
		}
		if(!empty($this->di[$name])){
			return;
		}
		$this->di[$name] = $dependency;
	}

	public function get($name)
	{
		if(empty($this->di[$name])){
			return null;
		}

		return $this->di[$name];
	}

}