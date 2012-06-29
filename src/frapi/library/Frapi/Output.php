<?php
class Frapi_Output_Exception extends Frapi_Exception {}

/**
 * Output class
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
 * This class contains the output context methods
 * and everything that is going to be used to load the
 * outputs.
 *
 * Note that if you have to add new output contexts, you
 * need to make sure that the Library/Output/Context.php
 * file gets created otherwise you'll end up with errors.
 *
 * Also, make sure to add it to the Library/AllFiles.php file.
 *
 * @uses      Frapi_Output_Exception
 * @license   New BSD
 * @package   frapi
 */
class Frapi_Output
{
    /**
     * This is a response that is set in the
     * OutputTYPE object.
     *
     * @var string $response  The response from the type object.
     */
    protected $response;

    /**
     * The action that the webservice requests
     *
     * @var string $action  The requested action
     */
    protected $action;

    /**
     * The type of output desired. See the OutputActionType
     *
     * @var string $type  The type of outputaction
     */
    public $type;

    /**
     * The MIME type to send in the headers.
     *
     * @var string
     **/
    public $mimeType = "text/plain";

    /**
     * This variable does not have a default value by default. It is
     * only used when the server-content-type is required and when it's
     * explicitely set by the controllers.
     *
     * @param string $outputFormat The output format to invoke.
     */
    public $outputFormat;

    /**
     * This method sets the value of the action requested
     * by the partner using the webservice.
     *
     * @param  string $action The action requested
     * @return object $this
     */
    public function setOutputAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * This method sets the output context type
     * requested by the partner using the webservice.
     *
     * @param  string $type The type of output (format)
     * @return Object $this
     */
    public function setOutputType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Send HTTP headers, namely HTTP Status Code.
     * We need the unformatted content to determine
     * what headers to send.
     *
     * @param Mixed $response
     *
     * @return Object $this
     **/
    public function sendHeaders($response)
    {
        header('HTTP/1.1 '.intval($response->getStatusCode()).' '.$response->getReasonPhrase());

        if ($response instanceof Frapi_Response) {
            $content_type = $response->getContentType();
            if ($content_type) {
                $this->mimeType = $content_type;
            }
        }

        header('Content-type: '.$this->mimeType.'; charset=utf-8');

        //IF debugging is turned on, then send cache info
        //headers - very useful for seeing what's happening.
        if (Frapi_Controller_Main::MAIN_WEBSERVICE_DEBUG) {
            $log = Frapi_Internal::getLog();

            $cache_info = 'Cache Fetches(' . $log['cache-get']['times'] . ') ' .
                          'Cache Stores(' . $log['cache-set']['times'] . ') ' .
                          'DbHandles(' . $log['db']['times'] . ')';

            header('X-Cache-Info: '.$cache_info);

            if (!empty($log['cache-get']['keys'])) {
                sort($log['cache-get']['keys']);
                header('X-Cache-Lookups: '.implode(' ', $log['cache-get']['keys']));
            }

            if (!empty($log['cache-set']['keys'])) {
                sort($log['cache-set']['keys']);
                header('X-Cache-Stores: '.implode(' ', $log['cache-set']['keys']));
            }
        }

        $cache = new Frapi_Internal();
        $cache = $cache->getCachedDbConfig();
        $allowCrossDomain = isset($cache['allow_cross_domain']) ? $cache['allow_cross_domain'] : false;

        if ($allowCrossDomain) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: *');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE,HEAD');
            header('Access-Control-Max-Age: 604800');
        }
        return $this;
    }

    /**
     * This method is simply a mashup to ease the life
     * of the internal developers. It is meant to call
     * both setOutputContextAction and setOutputContextType
     * at the same time so it is faster to develop with.
     *
     * @see $this->setOutputType($type);
     * @see $this->setOutputAction($action);
     *
     * @param  string $type   The type of outputcontext (format)
     * @param  string $action The action requested.
     * @return Object $this
     */
    public function setOutputTypeAction($type, $action)
    {
        $this->setOutputType($type);
        $this->setOutputAction($action);
        return $this;
    }

    public static function getMimeTypeMap()
    {
        try {
            if ($map = Frapi_Internal::getCached('Output.mimeMaps')) {
                return $map;
            }

            $cache = new Frapi_Internal();
            $mimetypes = $cache->getConfiguration('mimetypes')->getAll('mimetype');

            $outputs = $cache->getConfiguration('outputs')->getAll('output');

            $disabled = array();
            foreach ($outputs as $output) {
                if ($output['enabled'] == 0) {
                    $disabled[strtolower($output['name'])] = true;
                }
            }

            $map = array();
            foreach ($mimetypes as $mimetype) {
                if (isset($disabled[strtolower($mimetype['output_format'])])) {
                    continue;
                }
                $map[$mimetype['mimetype']] = $mimetype['output_format'];
            }
        } catch (Exception $e) {
            // No matter what happens for legacy reasons we fallback to the defaults
            return false;
        }

        Frapi_Internal::setCached('Output.mimeMaps', $map);

        return $map;
    }

    public static function getEnabledFormats()
    {
        try {
            if ($formats = Frapi_Internal::getCached('Output.formats-enabled')) {
                return $formats;
            }

            $cache   = new Frapi_Internal();
            $outputs = $cache->getConfiguration('outputs')->getAll('output');

            $formats = array();
            foreach ($outputs as $output) {
                if($output['enabled'] == 1) {
                    $formats[] = strtolower($output['name']);
                }
            }
        } catch (Exception $e) {
            return false;
        }

        Frapi_Internal::setCached('Output.formats-enabled', $formats);

        return $formats;
    }

    /**
     * Return a mimetype for a given extension
     *
     * @deprecated
     * @param string $format
     * @return string|false
     */
    public static function getMimeTypeByFormat($format)
    {
        $format = strtolower($format);

        $mimeMap = array(
            'xml' => 'application/xml',
            'json' => 'application/json',
            'html' => 'text/html',
            'js' => 'application/javascript',
            'printr' => 'text/php-printr',
        );

        return (isset($mimeMap[$format])) ? $mimeMap[$format] : false;
    }

    /**
     * This method will use the desired type
     * and instantiate the OutputContextObject
     * that is to be used according to the type.
     *
     * @param  string $type    The type of context to get.
     * @param  mixed  $options An array of options to pass (mimetype, outputformat) or false if nothing.
     *
     * @return Object The new OutputContext$TYPE object.
     */
    public static function getInstance($type, $options)
    {
        // We always override the extension if a content-type is requested.
        $type = isset($options['outputFormat'])
            ? $options['outputFormat']
            : $type;

        $class = 'Frapi_Output_' . strtoupper($type);
        $obj = new $class;

        $obj->type     = $type;

        $obj->mimeType = isset($options['mimetype']) && $options['mimetype']
            ? $options['mimetype']
            : $obj->mimeType;

        return $obj;
    }
}
