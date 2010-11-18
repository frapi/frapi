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

// HTTP_HOST is required by most tests
$_SERVER['HTTP_HOST'] = 'test';

/**
 * shorthand debug functions via Jaybill
 * http://jaybill.com/2007/10/01/the-most-useful-function-you-will-ever-use-in-the-zend-framework/
 * Modified by Jeremy Kendall
 */
            
function dd($val, $label="", $echo=true){
    d($val, $label, $echo);
    die();
}

function d($val, $label="", $echo=true){
    Zend_Debug::dump($val, $label, $echo);
}
