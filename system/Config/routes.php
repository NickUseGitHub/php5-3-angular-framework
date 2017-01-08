<?php

$routes = array();

$routes['/(^\/\b(A|a)pi\b)\/?/'] = array(
							'moduleName' => 'Api',
							'moduleNamespace' => 'Application\\Module\\Api',
							'module' => 'Application\\Module\\Api\\Module',
							'callback' => 'dispatch',
						);
$routes['/(^\/\b(C|c)ms\b\/\b(A|a)pi\b)\/?/'] = array(
							'moduleName' => 'CmsApi',
							'moduleNamespace' => 'Application\\Module\\CmsApi',
							'module' => 'Application\\Module\\CmsApi\\Module',
							'callback' => 'dispatch',
						);
$routes['/(^\/\b(C|c)ms\b)\/?/'] = array(
							'moduleName' => 'Cms',
							'moduleNamespace' => 'Application\\Module\\Cms',
							'module' => 'Application\\Module\\Cms\\Module',
							'callback' => 'dispatch',
						);
$routes['/(^\/\b(T|t)est\b)\/?/'] = array(
							'moduleName' => 'Test',
							'moduleNamespace' => 'Application\\Module\\Test',
							'module' => 'Application\\Module\\Test\\Module',
							'callback' => 'dispatch',
						);

return $routes;