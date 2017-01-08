<?php
namespace Application\Module;

interface InitController
{
	public function loadController($controllerHandler, $configValues);
}