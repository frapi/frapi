<?php

require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'library/Frapi/AllFiles.php';
set_error_handler(array('Frapi_Error', 'errorHandler'), E_ALL);

try {
    $controller = new Frapi_Controller_API();

    try {
        $controller->authorize();
        echo $controller->processAction()->processOutput();
    } catch (Frapi_Exception $e) {
        echo $controller->processError($e);
        exit;
    } catch (Frapi_Error $e) {
        echo $controller->processError($e);
        exit;
    } catch (Exception $e) {
        echo $controller->processError(new Frapi_Error($e));
        exit;
    }
} catch (Frapi_Exception $e) {
    // Whenever we get here, something went terribly wrong
    // in the core of FRAPI during initialisation phase.
    echo Frapi_Controller_Api::processInternalError($e);
} catch (Exception $e) {
    echo Frapi_Controller_Api::processInternalError(new Frapi_Error($e));
}
