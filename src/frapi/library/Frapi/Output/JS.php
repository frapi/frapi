<?php
/**
 * Output jsonp
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
 * This is the the output handler to generate some json data but also
 * attach this to a json handler through the json_callback parameter.
 *
 * If nothing is passed in the json_callback parameter, we create a (function() {});
 *
 * As you can see, this class is rather simple, it has a few
 * methods that are from the interface and it is also
 * using $response from the parent object.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Output_JS extends Frapi_Output implements Frapi_Output_Interface
{
    /**
     * The JSONP callback variable to append
     * to the response output as the function to call.
     *
     * @var $jsonpCallback The json function to write.
     */
    private $jsonpCallback = false;
    
    /**
     * Text Mime Type
     *
     * @var string
     */
    public $mimeType = 'text/javascript';
    
    /**
     * Populate the Output
     *
     * This method populates the $this->response
     * variable with the value returned from the
     * action.
     *
     * @param  Mixed $response Most of the times an array but could be
     *                         an stdClass
     * @param String $customTemplate The custom template file to use instead of the default one.
     *
     * @return void
     */
    public function populateOutput($response, $customTemplate = false)
    {
        if (isset($response['jsonp_callback'])) {
            $this->jsonpCallback = $response['jsonp_callback'];
            unset($response['jsonp_callback']);
        }
        
        $this->response = $response;
        return $this;
    }

    /**
     * Execute the output
     *
     * This method will basically return the value
     * of $this->response with the desired type.
     *
     * In this case... php serialized
     *
     * @return string callback(json_encode($this->response))
     */
    public function executeOutput()
    {
        $returnedData = json_encode($this->response);
        
        if ($this->jsonpCallback !== false) {
            return $this->jsonpCallback . '('.$returnedData.');';
        }
        
        /**
         * This has to look like:
         *
         * (function() { 
         *     return jsonobject;
         * });
         */
         return '(function() {' . PHP_EOL .
         '    return ' . $returnedData . '; ' . PHP_EOL .
         '});';
    }
}
