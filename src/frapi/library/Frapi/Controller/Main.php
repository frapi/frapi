<?php
/**
 * Main Controller
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
 * This is the controller that contains what the Web services controllers
 * are very likely to contain. It contains methods to help retrieve
 * parameters, help around.. it's the main controller..
 *
 *
 * @license   New BSD
 * @package   frapi
 */
class Frapi_Controller_Main
{
    /**
     * Constant used to retrieve extra caching information
     * through the headers. If this is set to true, you will
     * receive the size of the reuqest, the size of each
     * key in the cache and such information.
     *
     * @var bool Whether or not the webservice should be in debug mode.
     */
    const MAIN_WEBSERVICE_DEBUG  = false;

    /**
     * The request parameter
     *
     * The value of the superglobal $_REQUEST
     * variable from the web server.
     *
     * @var array $request  The $_REQUEST
     */
    protected $request;

    /**
     * The parameters in the request.
     *
     * The request gave us a bunch of parameters.
     *
     * @var array  An array of params coming straight from the
     *             $_REQUEST array.
     */
    protected $params;

    /**
     * The Files handlers
     *
     * You can make multiform-data posts to the server
     * so that is the files coming from the $_FILES array
     * in the upload.
     *
     * @var array $files   An array of file information ($_FILES);
     */
    protected $files;

    /**
     * The output format
     *
     * The output format is defaulted to XML as this is
     * at the time, the most widely used webservicer return format
     * and the most flexible and portable.
     *
     * @var string $format  The format in which you return the output.
     */
    protected $format = 'xml';

    /**
     * @var bool indicates output format was determined by file extension in URL
     */
    protected $formatSetByExtension = false;

    /**
     * @var string stores the mapped mimeType of the incoming content if found
     */
    protected $inputFormat;


    /**
     *
     * Each call has an action
     *
     * Each call has an action and it is stored in this
     * variable to invoke the requested context.
     *
     * @var string $action  The action invoked by the webservice.
     */
    protected $action;

    /**
     * Output Encoding Charset
     *
     * This is the variable that keeps the value of the default
     * encoding charset to be used when outputting the values returned
     * by the webservice.
     *
     * @var string $encoding  The encoding value, default 'utf8'
     */
    protected $encoding = 'utf-8';

    /**
     * The output context
     *
     * After invoking an action the output context is retrieved
     * and stored in this variable as the outputcontext object.
     *
     * @var Output $outputContext  The output context object.
     */
    protected $outputContext;

    /**
     * Action Context
     *
     * When you invoke an action, it's action context is invoked
     * and the object of that context is saved in this variable.
     *
     * @var Action  $actionContext  The action context object.
     */
    protected $actionContext;

    /**
     * Security Context
     *
     * When an action is invoked, no matter which one it is, it has to pass
     * the security verifications. This is the security context module object.
     *
     * @var Security  $security The security context
     */
    protected $security;

    /**
     * Authorization Context
     *
     * When invoking an action, is has to be either authorized or simply approved.
     * This is the object of the AuthorizationContext module.
     *
     * @var Authorization $authorization  The authorization context
     */
    protected $authorization;

    /**
     * A map of all the mimetypes to their output
     * format. In order to add a new mimetype, add it's
     * mimetype name and then add it's output as the associated
     * value.
     *
     * @deprecated The list here is left for legacy installations. Defining
     * @deprecated mimetypes in the admin will override these once defined.
     *
     * @var array An array of mimetypes and their output types.
     */
    protected $mimeMaps = array(
        'application/xml'  => 'xml',
        'text/xml'         => 'xml',
        'application/json' => 'json',
        'text/json'        => 'json',
        'text/html'        => 'html',
        'text/plain'       => 'json',
        'text/javascript'  => 'js',
        'text/php-printr'  => 'printr'
    );

    /*
     * @var array denotes which documents can be used as input
     */
    protected $allowedInputTypes = array('xml', 'json');

    /**
     * Constructor
     *
     * Upon invoking of the constructor, a few objects need to be created
     * in order to approve, authorize and secure the action contexts.
     *
     * This constructor sets up the Security, ErrorContainer, Authorization
     * and also sets the request parameters, files parameters, the format of the output
     * and of course the most important part which is the action/output contexts themselves.
     *
     * @warning IF-Clusterfuck.
     *
     * @see Security
     * @see ErrorContainer
     * @see Authorization
     * @params object $customAuthorization optional custom authorization extending Frapi_Authorization
     */
    public function __construct($customAuthorization=null)
    {
        try {
            $this->security      = new Frapi_Security();
            $this->authorization = ($customAuthorization instanceof Frapi_Authorization) ? $customAuthorization : new Frapi_Authorization();
            $this->router        = new Frapi_Router();


            $this->router->loadAndPrepareRoutes();
            $this->setInputFormat();

            $uri = $_SERVER['REQUEST_URI'];
            // For some reason, this is now a fatal
            // error in 5.3 and no longer a warning

            // in php (parse_url() with an http:// in the URL_PATH)...
            if (stristr($uri, '?') !== false) {
                $uri = substr($uri, 0, strpos($uri, '?'));
            }

            $query_path = parse_url($uri, PHP_URL_PATH);

            // @deprecated The use of extensions is considered deprecated
            //Query ending in .xxx may or may not be an output format
            $query_path_format = pathinfo($query_path, PATHINFO_EXTENSION);
            $format = $this->getParam('format');
            if (is_string($query_path_format) && strlen($query_path_format) || $format) {
                $extension = ($query_path_format) ? $query_path_format : $format;
                if (Frapi_Rules::validateOutputType($extension) === true) {
                    //Output format suffix is valid, remove from URL!
                    if ($query_path_format) {
                        $query_path = substr($query_path, 0, (strlen($extension) + 1)*-1);
                    }
                    $accept = Frapi_Output::getMimeTypeByFormat($extension);
                    if (isset($_SERVER['HTTP_ACCEPT'])) {
                        $_SERVER['HTTP_ACCEPT'] = $accept . ',' .$_SERVER['HTTP_ACCEPT'];
                    } else {
                        $_SERVER['HTTP_ACCEPT'] = $accept;
                    }
                }

                $query_path_format = $format = null;
            }

            if ($routed = $this->router->match($query_path)) {
                $_REQUEST = array_merge($_REQUEST, $routed['params']);

                $this->setAction(strtolower($routed['action']));
                $this->setRequest($_REQUEST);
            } else {
                $this->setRequest($_REQUEST);
                $this->setAction($this->getParam('action'));
            }

            $this->setFiles($_FILES);

            $this->authorization->setAuthorizationParams($this->getParams());
        } catch (Frapi_Exception $e) {
            // Something RONG happened. Need to tell developers.
            throw $e;
        } catch (Exception $e) {
            // Something else? Silence! I Keeel you.
        }
    }

    /**
     * Set Request
     *
     * This method returns the request
     * ($_REQUEST) mostly.
     *
     * @return mixed Array or String depending on how it feels.
     *
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get Files (GETTER)
     *
     * This method returns the request
     * ($_FILES) mostly.
     *
     * @return mixed Array or String depending
     *               On how it feels.
     *
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set files
     *
     * This method will set the parameters in
     * the filess variable.
     *
     * @param  array $params The params to set.
     * @return void
     */
    private function setFiles($params)
    {
        $this->files = $params;
    }

    /**
     * Get parameters
     *
     * This method returns an array of the parameters
     * passed.
     *
     * Is this similar to getRequest ? I have to check tmrw.
     * @todo ^
     * @return Mixed An array or a string of parameters
     */
    public function getParams()
    {
        $params = $this->request;

        /**
         * This certainly isn't a pure approach however it is a very
         * practical approach that will suit most people most of the times.
         *
         * Unhappy? Remove me.
         */

        $input = file_get_contents("php://input");
        parse_str($input, $puts);

        $inputFormat = $this->inputFormat;
        if(empty($inputFormat)) {
            $xmlJsonMatch = preg_grep('/\<|\{/i', array_keys($puts));
            $inputFormat = $this->getFormat();
        }

        /**
         * When doing parse_str("{json:string}") it creates an array like:
         * array(
         *  "{json:string}" => ""
         * )
         *
         * If args are also present along with the body, they are in the array
         * before the body.
         *
         * Checks if the last argument is an empty string, this + inputForm is
         * indicative of the body needing parsing.
         */
        if (end($puts) == '' && !empty($inputFormat) || !empty($xmlJsonMatch)) {
            /* attempt to parse the input */
            reset($puts);
            $requestBody = Frapi_Input_RequestBodyParser::parse(
                $inputFormat,
                $input
            );

            if (!empty($requestBody)) {
                $rootElement = array_keys($requestBody);

                // flatten the first element of the requestbody into the array
                // if it is itself an array and the only element
                // this handles a root element in the request body.
                if(count($requestBody) == 1 && is_array($requestBody[$rootElement[0]])) {
                    $params[$rootElement[0]] = true;
                    $requestBody = $requestBody[$rootElement[0]];
                }

                $params = array_merge($params, $requestBody);
            }
        } else if (!empty($puts)) {
            foreach ($puts as $put => $val) {
                $params[$put] = $val;
            }
        }

        $this->request = $params;
        return $this->request;
    }

    /**
     * Get a param
     *
     * This method will get a single param
     * out of the request variable.
     *
     * @param  string $key  The key to extract
     * @return Mixed Either value of the array key or false
     */
    public function getParam($key)
    {
        if (isset($this->request[$key])) {
            return $this->request[$key];
        }

        if (isset($this->files[$key])) {
            return $this->files[$key];
        }

        return false;
    }

    /**
     * Get the format
     *
     * Get the desired format. We are checking if it's
     * a valid one in setFormat. Getformat is really dumb.
     *
     * @return string $this->format  The format.
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Get default format from configuration
     *
     * A format (output type) has not been supplied
     * so try to get default from backend.
     *
     * @return String The format.
     */
    public function getDefaultFormatFromConfiguration()
    {

        if ($default_output_format = Frapi_Internal::getCached('Output.default-format')) {
            return $default_output_format;
        }

        $conf = Frapi_Internal::getConfiguration('outputs');
        $row  = $conf->getByField('output', 'default', '1');

        if (isset($row) && isset($row['name'])) {
            Frapi_Internal::setCached('Output.default-format', $row['name']);
            return $row['name'];
        }

        return Frapi_Controller_Api::DEFAULT_OUTPUT_FORMAT;
    }

    /**
     * Set format
     *
     * This method will check if the format is
     * an acceptable one, if so it'll set it to the
     * requested format.
     *
     * In the case where there are no format passed, we
     * default the value to 'xml'
     *
     * @param string $format The format to use.
     * @throws Frapi_Error
     */
    protected function setOutputFormat($format = false)
    {
        $format = strtolower($format);

        if ($format) {
            $typeValid = Frapi_Rules::validateOutputType($format);
            $this->format = $format;
        } else {
            throw new Frapi_Error (
                Frapi_Error::ERROR_INVALID_URL_PROMPT_FORMAT_NAME,
                Frapi_Error::ERROR_INVALID_URL_PROMPT_FORMAT_MSG,
                Frapi_Error::ERROR_INVALID_URL_PROMPT_FORMAT_NO
            );
        }
    }

    /**
     * @see self::setOutputFormat
     *
     * @deprecated
     * @param string $format
     */
    protected function setFormat($format = false)
    {
        $this->setOutputFormat($format);
    }

    /**
     * Get action
     *
     * Get the action passed, this will be used
     * in the ActionContext and OutputContext usage.
     *
     * Ex: getProfile, createAccount, login, etc.
     *
     * @return string $action  The action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the action
     *
     * This method is quite simple, it will set the
     * action requested. If it finds it in the list of
     * loginRequiredActions or PidRequiredActions it will
     * do the check and if it is false, it'll set and error
     *
     * @param string $action The requested action.
     */
    private function setAction($action = false)
    {
        $this->action = strtolower($action);
    }

    /**
     * reads the Content-Type header; sets request body format if a valid mime type exists
     *
     * @return void
     */
    public function setInputFormat()
    {
        $contentType = (isset($_SERVER['CONTENT_TYPE'])) ?
                $_SERVER['CONTENT_TYPE'] :
                null;

        if(!empty($contentType) &&
           isset($this->mimeMaps[$contentType]) &&
           in_array($this->mimeMaps[$contentType], $this->allowedInputTypes)) {
            $this->inputFormat = $this->mimeMaps[$contentType];
        }

    }

    /**
     * returns the expected input format
     *
     * @return string
     */
    public function getInputFormat()
    {
        return $this->inputFormat;
    }
}
