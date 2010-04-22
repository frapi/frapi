<?php
// Define path to application directory
define('ROOT_PATH',        dirname(dirname(dirname(__FILE__))));
define('APPLICATION_PATH', ROOT_PATH . '/admin/application');

// Define application environment
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

set_include_path('.' . PATH_SEPARATOR . ROOT_PATH . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR . get_include_path());

// Create application, bootstrap, and run
require_once 'Zend/Application.php';
$app = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/config/application.ini');
$app->bootstrap()->run();