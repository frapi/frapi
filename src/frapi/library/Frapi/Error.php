<?php
/**
 * Frapi Error.
 *
 *
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
 * This really is more an Error container but we are in
 * "Context" mode so let's keep it that way for code and structure
 * consistency
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Error extends Frapi_Exception
{
    /**
     * Error numbers and message
     *
     * This is a block of error messages and error
     * codes that are used thorough the application.
     *
     */
    const ERROR_INVALID_SECRET_KEY_NO          = 403;
    const ERROR_INVALID_URL_PROMPT_FORMAT_NO   = 400;
    const ERROR_INVALID_ACTION_REQUEST_NO      = 404;
    const ERROR_INVALID_PARTNER_ID_NO          = 403;
    const ERROR_MISSING_REQUEST_ARG_NO         = 412; // Precondition Failed.

    const ERROR_INVALID_SECRET_KEY_MSG         = 'Invalid secret key';
    const ERROR_INVALID_URL_PROMPT_FORMAT_MSG  = 'Invalid format';
    const ERROR_INVALID_ACTION_REQUEST_MSG     = 'Invalid requested action';
    const ERROR_INVALID_PARTNER_ID_MSG         = 'Invalid user id';
    const ERROR_MISSING_REQUEST_ARG_MSG        = 'Missing required parameters';

    const ERROR_INVALID_SECRET_KEY_NAME         = 'ERROR_INVALID_SECRET_KEY';
    const ERROR_INVALID_URL_PROMPT_FORMAT_NAME  = 'ERROR_INVALID_URL_PROMPT_FORMAT';
    const ERROR_INVALID_ACTION_REQUEST_NAME     = 'ERROR_INVALID_ACTION_REQUEST';
    const ERROR_INVALID_PARTNER_ID_NAME         = 'ERROR_INVALID_PARTNER_ID';
    const ERROR_MISSING_REQUEST_ARG_NAME        = 'ERROR_MISSING_REQUEST_ARG';

    /**
     * Error Labels
     *
     * Sometimes, you may need to pass the "at" error message
     * this constant contains a sprintf formatted string to be
     * ready to output.
     */
    const ERROR_MISSING_REQUEST_ARG_LABEL           = 'Required Parameters: %s';

    /**
     * Are errors statically loaded into class?
     *
     * We use this bool variable because the self::$_errors
     * array may be EMPTY but it may have been loaded from
     * database so we keep a note of this.
     *
     * @var Boolean
     **/
    private static $_statically_loaded = false;

    /**
     * Stores error codes and messages.
     *
     * @var Array
     **/
    private static $_errors = Array();

    /**
     * Constructor
     *
     * This constructs a Frapi_Error message to return to the users.
     *
     * @todo add some doc examples of the new errors.
     *
     * @param string $error_name Name of error.
     * @param string $error_msg  The actual message of the error.
     * @param int    $http_code  This might be hard to grasp however, we are in a web
     *                           industry dealing with the web. The code you are sending
     *                           to your exception should really be represented by the
     *                           HTTP Code returned to your users.
     *
     * @return void
     */
    public function __construct($error_name, $error_msg = false, $http_code = false)
    {
        if ($error_name instanceof Exception) {
            $error = self::_get($error_name->getCode(), $error_name->getMessage(), 400);
        } else {
            $error = self::_get($error_name, $error_msg, $http_code);
        }

        parent::__construct($error['message'], $error['name'], $error['http_code']);
    }

    /**
     * Very basic error handler
     *
     * This method is to catch all usual errors thrown from PHP
     * and display them in an API looking way.
     *
     * @param  int    $errno  The error number.
     * @param  string $errstr The error message
     * @param  string $errfile The file where the error happened.
     * @param  int    $errline The line at which the error happened in $errfile.
     *
     * @throws Frapi_Error
     * @return void
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $errorType = 'Fatal';

        switch ($errno) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_USER_ERROR:
                $errorType = 'Fatal';
                break;
            case E_WARNING:
            case E_CORE_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
                $errorType = 'Warning';
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $errorType = 'Notice';
                break;
            case E_PARSE:
                $errorType = 'Parse';
                break;
            case E_STRICT:
                $errorType = 'Strict';
                break;
            case E_RECOVERABLE_ERROR:
                $errorType = 'Recoverable';
                break;
            default:
                $errorType = 'Unknown';
                break;
        }

        throw new Frapi_Error(
            'PHP ' . $errorType . ' error',
            $errstr . ' (Error Number: '.$errno.'),' .
                      ' (File: ' . $errfile . ' at line ' . $errline . ')',
            400
        );
    }

    /**
     * Used to implement ::code(), ::msg() and ::message() ::name()
     * Errors are retrieved as such:
     * <ul>
     *  <li>If in self::$_errors return</li>
     *  <li>If in APC return</li>
     *  <li>Else get from DB, store APC.</li>
     * </ul>
     *
     * @return Mixed Int code or String message.
     **/
    public static function __callStatic($function_name, $args)
    {
        $error = self::_get(current($args));

        switch (strtolower($function_name)) {
            case 'msg':
            case 'message':
                return $error['message'];
                break;
            case 'code':
            case 'http_code':
                return $error['http_code'];
                break;
            case 'name':
                return $error['name'];
                break;
        }
    }

    /**
     * Private function to get error (statically, APC, or DB).
     *
     * This function tries to locate the error in progressively
     * slower datastores (static class variable, APC, database)
     * and will store the loaded errors in the faster stores.
     *
     * @param string $error_name Name of error.
     * @param string $error_msg  The actual message of the error.
     * @param int    $http_code  This might be hard to grasp however, we are in a web
     *                           industry dealing with the web. The code you are sending
     *                           to your exception should really be represented by the
     *                           HTTP Code returned to your users.
     *
     * @return array An array with the content of the error.
     */
    private static function _get($error_name, $error_msg = false, $http_code = false)
    {
        if (!self::$_statically_loaded) {
            $errors = Frapi_Internal::getCached('Errors.user-defined');

            if ($errors) {
                self::$_errors = $errors;
            } elseif ($errors = self::_getErrorsFromDb()) {
                self::$_errors = $errors;
                Frapi_Internal::setCached('Errors.user-defined', $errors);
            }

            self::$_statically_loaded = true;
        }

        if (isset(self::$_errors[$error_name])) {
            $error = self::$_errors[$error_name];

            if ($error_msg !== false) {
                $error['message'] = $error_msg;
            }

            if ($http_code !== false) {
                $error['http_code'] = $http_code;
            }

            return $error;
        }

        return array(
            'name'      => $error_name,
            'message'   => $error_msg !== false ? $error_msg : $error_name,
            'http_code' => $http_code !== false  ? $http_code : '400',
        );
    }

    /**
     * Get errors from database.
     *
     * This method fetches the errors from the database (XML)
     * key-values which then get cached when used in self::_get
     *
     * @return Array An array of errors
     */
    private static function _getErrorsFromDb()
    {
        $conf          = Frapi_Internal::getConfiguration('errors');

        $conf_errors   = $conf->getAll('error');

        $errors = array();

        if (is_array($conf_errors) && !empty($conf_errors)) {
            foreach ($conf_errors as $errKey => $error) {
                $errors[$error['name']] = $error;
            }
        }

        return $errors;
    }
}
