<?php
require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'library/Frapi/AllFiles.php';
set_error_handler(array('Frapi_Error', 'errorHandler'), E_ALL);

$controller = new Frapi_Controller_API();

try {
    $controller->authorize();
    echo $controller->processAction()->processOutput();
} catch (Frapi_Exception $e) {
    echo $controller->processError($e);
    exit;
}
