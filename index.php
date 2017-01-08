<?php
require 'vendor/autoload.php';

use Application\Core\Application;

if (!isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = 'testflight.dev';
}

if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'testflight.dev';
}

/***
  *		constant variables
  **/
if (!defined('APPLICATION_PATH')) {
	define('APPLICATION_PATH', __DIR__);
}
require_once APPLICATION_PATH."/system/Config/constant.php";

/***
  *     config php
  **/
ini_set('display_errors',1);
setlocale(LC_ALL, 'Thai');
date_default_timezone_set("Asia/Bangkok");

Application::dispatch();