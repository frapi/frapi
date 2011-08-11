<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://getfrapi.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@getfrapi.com so we can send you a copy immediately.
 *
 * @license   New BSD
 * @package   frapi-admin
 */
class ErrorController extends Lupin_Controller_Base
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');


        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()
                     ->setRawHeader('HTTP/1.1 404 Not Found');

                // ... get some output to display...
                break;
            default:
                // application error; display error page, but don't change
                // status code

                // ...

                // Log the exception:
                $e= $errors->exception;
                //$log = new Zend_Log(
                //    new Zend_Log_Writer_Stream(
                //        '/tmp/applicationException.log'
                //    )
                //);
                //$log->debug($e->getMessage() . "\n" .
                //            $e->getTraceAsString());

                if (APPLICATION_ENV == 'development') {
            -        $msg   = $e->getMessage();
            -        $trace = $e->getTraceAsString();
            -        die("<div>Error: $msg<p><pre>$trace</pre></p></div>");
                }
                break;
        }
    }
}