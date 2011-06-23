<?php
// Define path to application directory
define('ROOT_PATH',        dirname(dirname(dirname(__FILE__))));
define('APPLICATION_PATH', ROOT_PATH . DIRECTORY_SEPARATOR .
                           'admin' . DIRECTORY_SEPARATOR.'application');

// Define application environment
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


set_include_path('.' . PATH_SEPARATOR . ROOT_PATH . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR . ROOT_PATH . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'PEAR' . PATH_SEPARATOR . get_include_path());

// Create application, bootstrap, and run
require_once 'Zend/Application.php';

require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'library' .
          DIRECTORY_SEPARATOR . 'Frapi' . DIRECTORY_SEPARATOR . 'AllFiles.php';

$app = new Zend_Application(
    APPLICATION_ENV, APPLICATION_PATH . DIRECTORY_SEPARATOR . 'config' .
    DIRECTORY_SEPARATOR.'application.ini'
);

$app->bootstrap()->run();
