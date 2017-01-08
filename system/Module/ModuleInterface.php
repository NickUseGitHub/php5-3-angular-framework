<?php
namespace Application\Module;

interface ModuleInterface
{

	public function __construct($uriInfo, $strModule);
	public function __destruct();

}