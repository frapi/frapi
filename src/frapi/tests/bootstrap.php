<?php

require_once realpath(dirname(__FILE__) . '/../library/Frapi/AllFiles.php');

define('APPLICATION_PATH', ROOT_PATH . '/admin/application');
define('APPLICATION_ENV', 'testing');

set_include_path(
    implode(PATH_SEPARATOR, 
        array(
            ROOT_PATH . DIRECTORY_SEPARATOR . 'library',
            dirname(__FILE__) . DIRECTORY_SEPARATOR .'mock',
            get_include_path()
        )
    )
);

// Create application, bootstrap, and run
require_once 'Zend/Application.php';
$app = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/config/application.ini');
