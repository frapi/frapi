<?php

/**
 * Action class
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
 * This class contains the action context methods
 * and everything that is going to be used to load the
 * actions.
 *
 * It also contains an array that says which fields need
 * to be logged in in order to use and which fields simply need
 * a username/email and secretkey (Set from the admin).
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 * @uses      Frapi_Action_Exception
 */ 
class Frapi_Action
{
    // Types used in getParam for casting
    const TYPE_INT          = 'int';
    const TYPE_INTEGER      = 'int';
    const TYPE_LONG         = 'int';
    const TYPE_STRING       = 'string';
    const TYPE_FLOAT        = 'float';
    const TYPE_DOUBLE       = 'double';
    const TYPE_BOOL         = 'bool';
    const TYPE_ARRAY        = 'array';
    const TYPE_OBJECT       = 'object';
    const TYPE_SQL          = 'sqlsafe';
    const TYPE_OUTPUT       = 'output';
    const TYPE_SAFESQLARRAY = 'safesqlarray';
    const TYPE_OUTPUTSAFE   = 'outputsafe';
    const TYPE_FILE         = 'file';

    /**
     * The value of the current action being
     * executed by the webservice.
     *
     * @var string $action  The action.
     */
    protected $action;

    /**
     * This is the pid of the person that has been logged.
     *
     * @var string   The session saved pid
     */
    protected $sessionPid;

    /**
     * The parameters passed to the action
     * context object.
     *
     * @var mixed $params This can be a string or an array
     *                    but usually this comes from the $_REQUEST
     */
    protected $params;

    /**
     * Those are the files uploaded to the server.
     *
     * @var mixed $params  The values of the $_FILES variable
     */
    protected $files;

    /**
     * This is something that is going to be used only
     * in the child classes to store the error message
     *
     * @var ErrorContext Stores an error context
     */
    protected $errors;

    /**
     * This is stored in sessionID passed.
     *
     * @var string $uid  The user uid (Internally used only)
     */
    protected $uid;
    
    /**
     * If one decides to use a custom template in the XML or HTML output
     * then this variable will be set.
     *
     * @var string The name of the custom template to load from custom/Output/{type}/custom/
     */
    protected $customTemplate = false;
    
    /**
     * Get an instance of the desired type of Action
     * Context using the action passed to it
     *
     * @param  string $action The action context to load
     * @return Action instance
     */
    public static function getInstance($action)
    {
        $directory = CUSTOM_ACTION . DIRECTORY_SEPARATOR;
        $filePlain = ucfirst(strtolower($action));
        $file      = $directory . $filePlain . '.php';
        $class     = 'Action_' . $filePlain;

        if (!file_exists($file) || !is_readable($file)) {
            throw new Frapi_Action_Exception (
                Frapi_Error::ERROR_INVALID_ACTION_REQUEST_MSG,
                'ERROR_INVALID_ACTION_REQUEST',
                Frapi_Error::ERROR_INVALID_ACTION_REQUEST_NO,
                null,
                400
            );
        }

        if (!class_exists($class, false)) {
            require $file;
        }

        return new $class;
    }

    /**
     * Set the action context parameters
     *
     * @param array $params The params to populate xml with.
     */
    public function setActionFiles($params)
    {
        $this->files = $params;
        return $this;
    }

    /**
     * Set the action context parameters
     *
     * @param array $params The params to populate xml with.
     */
    public function setActionParams(array $params)
    {
        $this->action = isset($params['action']) ? $params['action'] : null;
        
        $this->params = $params;
        return $this;
    }

    /**
     * Set the action context action method to be called, for reference
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }
    
    /**
     * Retrieve the action we are invoking.
     *
     * This method is used to retrieve the name of the action we are
     * currently invoking. This is mainly used by the likes of the custom
     * authentication.
     *
     * @return string The action being invoked.
     */
    public function getAction()
    {
        return $this->action;
    }
     
    /**
     * Set the template file to use
     *
     * This method is used to set the custom template file to use
     * instead of the default Actionname.xml.tpl or Actionname.html.tpl
     *
     * @param string $customTemplateFileName The template file to load.
     * @return void
     */
    public function setTemplateFileName($customTemplateFileName)
    {
        $this->customTemplate = $customTemplateFileName;
    }
    
    /**
     * Get the template to load
     *
     * This method fetches the template filename to use and
     * returns it's value.
     *
     * @return mixed Either false or a string with the name of the file.
     */
    public function getTemplateFileName()
    {
        return $this->customTemplate;
    }
    
    /**
     * This method validates that all your parameters
     * required for your action to run are present.
     *
     * If they are not, uhoh! Return a new error.
     *
     * @param  Array   $requiredParameters An array of the required params
     * @return Mixed   Either an ErrorContext with missing param or true
     */
    protected function hasRequiredParameters($requiredParameters)
    {
        $missingParams = array();
        foreach ($requiredParameters as $param) {
            if (!isset($this->params[$param]) && !isset($this->files[$param])) {
                $missingParams[] = $param;
            }
        }

        if (!empty($missingParams)) {
            $missingParamsString = implode(', ', $missingParams);
            throw new Frapi_Action_Exception (
                Frapi_Error::ERROR_MISSING_REQUEST_ARG_MSG,
                'ERROR_MISSING_REQUEST_ARG',
                Frapi_Error::ERROR_MISSING_REQUEST_ARG_NO,
                sprintf(Frapi_Error::ERROR_MISSING_REQUEST_ARG_LABEL, $missingParamsString),
                400
            );
        }

        return true;
    }

    // The RESTful actions
    public function executeGet()    { return $this->executeAction(); }
    public function executePut()    { return $this->executeAction(); }
    public function executePost()   { return $this->executeAction(); }
    public function executeDelete() { return $this->executeAction(); }
    public function executeHead()   { return $this->executeAction(); }

    /**
     * Return all the parameters
     *
     * Return all the request parameters we are currently holding
     * in $this->params
     *
     * @return array An array of request parameters and files maybe.
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Return the files uploaded
     *
     * This method is exactly like $this->getParams() however
     * it returns a list of variables that were stored about the
     * uploaded files instead of parameters.
     *
     * @return array An array of uploaded file information
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * This method will return the value of the
     * parameter. If it's not set then it returns null.
     *
     * The function is coded such that FALSE is known to indicate
     * non-existence of param in $params, NULL indicates that
     * param was empty and no default or error was supplied.
     *
     * Exhibits cascading behaviour so that any, and all parameters except
     * $key can be left null, to choose not to use those features.
     * For instance, ::getParam('get-key', null, 'AA886622', null)
     * will use the default type self::TYPE_STRING and if the param does
     * not exist then it will return 'AA886622' AS IS! Type conversions
     * are never applied to the default value.
     *
     * @param string $key        The name of the param key
     * @param string $type       The type of casting to do when returning
     *                           the requested parameter
     * @param Mixed  $default    The default value, if param is empty.
     * @param String $error_name The error name to raise, as last resort. 
     *
     * @return Mixed String when the key is valid, ErrorContext if not valid, or null if not set.
     */
    protected function getParam($key, $type = self::TYPE_STRING, $default = null, $error_name = null) 
    {
        $param = isset($this->params[$key]) ? $this->params[$key] : null;

        switch ($type) {
            case self::TYPE_FILE;
                $param = null;
                if (isset($this->files) && isset($this->files[$key])) {
                    $param = $this->files[$key];
                }

                break;
            case self::TYPE_STRING:
                $param = (string)$param;
                break;
            case self::TYPE_INTEGER:
                $param = (int)$param;
                break;
            case self::TYPE_FLOAT:
            case self::TYPE_DOUBLE:
                $param = (float)$param;
                break;
            case self::TYPE_ARRAY:
                $param = (array)$param;
                break;
            case self::TYPE_OBJECT:
                $param = (object)$param;
                break;
            case self::TYPE_SQL:
                $param = mysql_escape_string($param);
                break;
            case self::TYPE_OUTPUT:
                $param = htmlentities($param, ENT_QUOTES, 'UTF-8');
                break;
            case self::TYPE_OUTPUTSAFE:
                // OMFG Skype: (Puke). Code gangbang.
                $param = htmlentities(mysql_escape_string($param), ENT_QUOTES, 'UTF-8');
                break;
            case self::TYPE_SAFESQLARRAY:
                $tmpArray = array();
                foreach ((array)$param as $val => $par) {
                    $val = mysql_escape_string($val);
                    $par = mysql_escape_string($par);
                    $tmpArray[$val] = $par;
                }
                
                $param = $tmpArray;
                break;
            default:
                $param = null;
         }
                 
         return $param;
     }
}
