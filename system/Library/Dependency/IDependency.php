<?php

namespace Application\Library\Dependency;

/**
* 
*/
interface IDependency
{
	public function __construct();
	public function __destruct();
}