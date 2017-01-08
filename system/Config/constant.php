<?php 

define('SYSTEM_PATH', APPLICATION_PATH."/system/");
define('DATA_PATH', APPLICATION_PATH."/assets/");
define('CONFIG_PATH', SYSTEM_PATH."Config/");
define('GLOBAL_VIEW_PATH', SYSTEM_PATH."View/");

define('PROJECT_NAME', "cellox");
define('BASE_URL', $_SERVER['HTTP_HOST']);

define('ERROR_CONTROLLER', "Error");
define('DEFAULT_CMS_CONTROLLER', "App");
define('DEFAULT_METHOD', "index");