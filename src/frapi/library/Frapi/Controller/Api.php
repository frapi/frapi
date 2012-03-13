<?php
/**
 * Initial API Controller
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
 * API Controller
 *
 * Everytime the index is called, this controller is
 * also going to be called.
 *
 * It is getting most of it's methods from the MainController
 * and it is much cleaner now.
 *
 * @license   New BSD
 * @package   frapi
 */
class Frapi_Controller_Api extends Frapi_Controller_Main
{
    /**
     * In case we have something undefined
     * the default output format is simply xml
     */
    const DEFAULT_OUTPUT_FORMAT = 'xml';

    /**
     * This is the detected mimetypes and the options
     * associated with it.
     *
     * @var array An array of mimetype and associated format.
     */
    public $options;

    /**
     * Ctor
     *
     * This is setting the model, and many variables as such as the parameters.
     *
     *
     * @see $this->setFormat()
     * @see Error
     * @uses Error
     * @param Frapi_Authorization $customAuthorization optional custom authorization
     */
    public function __construct($customAuthorization=null)
    {
        parent::__construct($customAuthorization);
        $this->options = $this->detectAndSetMimeType();
    }

    /**
     * Get Action  Instance
     *
     * Get an install of the context action
     * by it's type.
     *
     * This method will use the Rules to make
     * sure that the type is an allowed type.
     *
     * @see    Frapi_Action::getInstance($type)
     * @param  string $type The type of action to get.
     *
     * @return mixed         Action Instance of the ActionType
     *                       or false if the type is not valid
     */
    protected function getActionInstance($type)
    {
        $this->actionContext =  Frapi_Action::getInstance($type);
        return $this->actionContext;
    }

    /**
     * Get Output Instance
     *
     * Get an install of the context output by it's type.
     *
     * This method will use the Rules to make
     * sure that the type is an allowed type.
     *
     * @see    Frapi_Output::getInstance($type)
     * @param  string $type  The type of output to get.
     *
     * @return mixed         Output Instance of the OutputType
     *                       or false if the type is not a valid type.
     */
    protected function getOutputInstance($type)
    {
        $this->outputContext = Frapi_Output::getInstance($type, $this->options);
        return $this->outputContext;
    }

    /**
     * Process Output
     *
     * This method will process the output by getting
     *
     * @return string  The output
     */
    public function processOutput()
    {
        // We check if we have errors, if we don't then we can process normally
        /**
         * Handle the headers and depending on whether they are a PUT, POST, DELETE, GET
         * we should invoke:
         *   $this->actionContext->executePost();
         *   $this->actionContext->executePut();
         *   $this->actionContext->executeDelete();
         *   $this->actionContext->executeGet();
         *   $this->actionContext->executeHead();
         */
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : false;

        $action = 'executeAction';

        if ($method !== false) {
            $action = 'execute' . ucfirst(strtolower($method));
        }

        $response = $this->actionContext->setAction($action)->$action();

        // Make sure we use a Frapi_Response.
        if (!$response instanceof Frapi_Response) {
            $response = new Frapi_Response(
                array(
                    'data' => $response
                )
            );
        }


        /**
         * Here we look for the temporary params so we can
         * automatically replace the jsonp_callback and handle
         * it in the OutputHandlers transparently.
         */
        $tmpParams = $this->actionContext->getParams();
        if (isset($tmpParams['jsonp_callback'])) {
            $response->setData(
                $response->getData() +
                array('jsonp_callback' => $tmpParams['jsonp_callback'])
            );
        }

        unset($tmpParams);

        /**
         * If the action result is NOT an instance of
         * Error, we can assume that it's valid
         * output so keep going and output the result
         */
        return $this->getOutputInstance($this->getFormat())
                    ->setOutputAction($this->getAction())
                    ->populateOutput(
                        $response->getData(),
                        $this->actionContext->getTemplateFileName())
                    ->sendHeaders($response)
                    ->executeOutput();
    }

    /**
     * Process Frapi Errors
     *
     * This method will process the FRAPI Errors, pass them to the
     * output handler, and format it correctly.
     *
     * @param Frapi_Exception $e  The frapi exception to use
     * @return object The response object.
     */
    public function processError(Frapi_Exception $e)
    {
        return Frapi_Controller_Api::processInternalError($e);
    }

    /**
     * Statically Process Frapi Errors
     *
     * This method will process the FRAPI Errors, pass them to the
     * output handler, and format it correctly.
     *
     * This method is in fact a hack. Whenever we instantiate the controller
     * object from the source — index.php in this case — we can't try and
     * catch the same controller object because the exception might in fact
     * be thrown directly from within the constructor thus invalidating
     * and sending the self $controller object out of scope. Thence this hack
     * that instantiates it's own controller, ignores the exception thrown
     *
     * This also allows us to catch syntax errors before the constructor
     * is invoked and allows us to handle the errors gracefully.
     *
     * @param Frapi_Exception $e  The frapi exception to use
     * @return object The response object.
     */
    public static function processInternalError(Frapi_Exception $e)
    {
        try {
            $controller = new Frapi_Controller_Api();

            return $controller->getOutputInstance($controller->getFormat())
                          ->setOutputAction('defaultError')
                          ->populateOutput($e->getErrorArray())
                          ->sendHeaders($e)
                          ->executeOutput();
        } catch (Exception $e) {
            // This is a hack to intercept anything that may
            // have happened before the internal error collection
            // during the initialisation process.
            //
            // If we got here, we have no controller and cannot properly handle
            // output, so we just send a 500 and die.


            header("HTTP/1.0 500 Internal Server Error");
            exit;
        }


    }

    /**
     * Process Action
     *
     * This method will process the action
     * but will not return anything. It will simply
     * invoke the getActionInstance and keep the
     * object state in place so the processOutput can
     * verify the data and output whichever it has to output.
     *
     * @see    $this->getActionInstance
     * @return Object $this
     */
    public function processAction()
    {

        // The state of the action context is now at a state where it can be used.
        $e = $this->getActionInstance($this->getAction())
                  ->setActionParams($this->getParams())
                  ->setActionFiles($this->getFiles())
                  ->setAcceptParams(
                      isset($this->options) && is_array($this->options) 
                      ? $this->options : array()
                  );

        return $this;
    }

    /**
     * Get the format for the outpt.
     *
     * If the format is not set, get the default format
     * from the self::DEFAULT_OUTPUT_FORMAT constant.
     */
    public function getFormat()
    {
        $format = parent::getFormat();
        $this->format = isset($format) ? $format : self::DEFAULT_OUTPUT_FORMAT;
        return $this->format;
    }

    /**
     * Authorize
     *
     * This method authenticates by verifying if the requested action
     * is made by a **partner** or if it's a public action.
     *
     * Partner is a term I grabbed when working at mobivox. They are not
     * users in the term that they need to register and keep a logged in
     * session but stateless "users". I decided to keep that term since
     * it makes more sense than "statelessUser";
     *
     * @return boolean   Either it's authorized or not.
     */
    public function authorize()
    {
        // If this is a public action, it doesn't need authorization.
        if (Frapi_Rules::isPublicAction($this->getAction())) {
            return true;
        }

        //For Basic HTTP Auth, use headers automatically filled by PHP, if available.
        $headers = $_SERVER;

        $auth_params = array(
            'digest' => isset($_SERVER['PHP_AUTH_DIGEST']) ? $_SERVER['PHP_AUTH_DIGEST'] : null
        );

        // First step: Set the state of the context objects.
        $partner =
            $this->authorization
                 ->getPartner()
                 ->setAction($this->getAction())
                 ->setAuthorizationParams($auth_params);

        /**
         * Second step: Run the authorization, error in case of
         * error in returned values, else it's just a true.
         */
        $partnerAuth = $partner->authorize();

        /**
         * Step Three: If we have no  partner
         * auth we return an error of invalid requested action
         * because if the action is not found in the contexts
         * it returns true.
         *
         * If it is found but has an error, it throws Frapi_Error
         *
         * If it is ok, it returns true.
         */

        if (!$partnerAuth) {
            throw new Frapi_Error(
                Frapi_Error::ERROR_INVALID_ACTION_REQUEST_NAME,
                Frapi_Error::ERROR_INVALID_ACTION_REQUEST_MSG,
                Frapi_Error::ERROR_INVALID_ACTION_REQUEST_NO
            );
        }

        return true;
    }

    /**
     * Detect and set the mimetype
     *
     * This method is used to detect the CONTENT-TYPE passed
     * to the API, assign this mimetype to an output type and move on.
     *
     * In the event where a content type isn't found or isn't mapped
     * we return false and move on with our lives.
     *
     * @return mixed Either false or an array of mimetype and outputformat
     */
    public function detectAndSetMimeType()
    {
        $types = $this->parseAcceptHeader();

        if(empty($types)) {
            return false;
        }

        if ($this->formatSetByExtension) {
            return array(
                'mimeType' => $this->setformat(strtolower($this->format)),
                'outputFormat' => $this->format,
            );
        }

        $mimetypes = $this->parseMimeTypes();

        foreach($types as $type) {
            if (isset($this->mimeMaps[$type['mimetype']])) {
                $outputFormat = strtoupper($this->mimeMaps[$type['mimetype']]);

                $this->setFormat(strtolower($outputFormat));

                $return = $type['params'] + array(
                    'mimeType'     => $type['mimetype'],
                    'outputFormat' => $outputFormat
                );

                return $return;
            } else {
                foreach ($mimetypes as $mimetype) {
                    $matches = array();
                    if (preg_match($mimetype['pattern'], $type['mimetype'], $matches)) {
                        $return = array(
                            'mimeType' => $type['mimetype'],
                        );

                        if ($mimetype['output_format'][0] == ':') {
                            $format = substr($mimetype['output_format'], 1);
                            if (!empty($matches[$format])) {
                                $return['outputFormat'] = $matches[$format];
                            } else {
                                $return['outputFormat'] = self::DEFAULT_OUTPUT_FORMAT;
                            }
                        } else {
                            $return['outputFormat'] = $mimetype['output_format'];
                        }

                        foreach ($mimetype['params'] as $param) {
                            $return[$param] = $matches[$param];
                        }

                        return $return;
                    }
                }
            }
        }

        return array();
    }

    /**
     * parse Accept header and sort Accept values by q priority
     *
     * @return array
     */
    protected function parseAcceptHeader()
    {
        if (!isset($_SERVER['HTTP_ACCEPT'])) {
            return array();
        }

        $acceptLowPriority = $acceptHighPriority = array();

        $types = explode(',', $_SERVER['HTTP_ACCEPT']);
        foreach($types AS $type) {
            $typeMatch = preg_match('/(?P<mimetype>[a-z\*]+\/[a-z\+\-\*]+)(?:(?:;)(?P<params>.*?$))?/i', $type, $typeComponents);
            if(!$typeMatch) {
                continue;
            }

            $params = array();
            if (isset($typeComponents['params'])) {
                $params = $this->parseAcceptHeaderParams($typeComponents['params']);
            }

            $priority = isset($params['q']) ? $params['q'] : 1;

            $mimetype = array('mimetype' => $typeComponents['mimetype'], 'params' => $params);

            if($priority === 1) {
                $acceptHighPriority[] = $mimetype;
            } else {
                $acceptLowPriority[(10*$priority)] = $mimetype;
            }
        }
        krsort($acceptLowPriority);
        return array_merge($acceptHighPriority, $acceptLowPriority);
    }

    /**
     * Parse accept headers params, e.g. charset=utf-8
     *
     * @param string $params
     */
    protected function parseAcceptHeaderParams($params)
    {
        $segments = explode(";", $params);

        $params = array();
        foreach ($segments as $segment) {
            $param = array();
            preg_match('/^(?P<name>.+?)=(?P<quoted>"|\')?(?P<value>.*?)(?:\k<quoted>)?$/', $segment, $param);

            $params[$param['name']] = $param['value'];
        }

        return $params;
    }

    /**
     * Parse mimetype for params
     *
     * @param string $mimetype
     *
     * @return array
     */
    protected function parseMimeTypes()
    {
        $cache = new Frapi_Internal();
        $mimetypes = $cache->getConfiguration('mimetypes')->getAll('mimetype');

        $patterns = array();

        foreach($mimetypes as $mimetype) {
            if (strpos($mimetype['mimetype'], ':') === false) {
                continue;
            }

            $segments = preg_split("@/|\.|\+@", $mimetype['mimetype']);
            $mimetype['mimetype'] = preg_quote($mimetype['mimetype']);

            foreach ($segments as $segment) {
                if ($segment[0] == ':') {
                    $param = substr($segment, 1);
                    $params[] = $param;
                    $mimetype['mimetype'] = str_replace(preg_quote($segment), '(?P<' .preg_quote($param). '>.*?)', $mimetype['mimetype']);
                }
            }

            // Don't add mimetypes that didn't have params
            if (sizeof($params)) {
                $patterns[] = $mimetype + array('pattern' => "@^{$mimetype['mimetype']}$@", 'params' => $params);
            }
        }

        return $patterns;
    }
}

