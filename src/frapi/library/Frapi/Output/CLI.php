<?php
/**
 * Output CLI
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
 * CLI output. This is mainly used to
 * work thorough the tests.
 *
 * @uses      Frapi_Output
 * @uses      Frapi_Output_Interface
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Output_CLI extends Frapi_Output implements Frapi_Output_Interface
{
    /**
     * Text Mime Type
     *
     * @var string
     */
    protected $mimeType = 'text/plain';
    
    /**
     * Re-defining sendHeaders not to send headers
     * in CLI environment.
     *
     * @return Object $this
     **/
    public function sendHeaders()
    {
        //Do nothing!
        //Could output the status code for
        //information like "Header Would Have Been Sent: xxx"
        //but nothing for now.
        return $this;
    }
    
    /**
     * Populate the Output
     *
     * This method populates the $this->response
     * variable with the value returned from the
     * action.
     *
     * @param Mixed $response Most of the times an array but could be
     *                        an stdClass
     * @return Object $This object
     */
    public function populateOutput($response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Execute the output
     *
     * This method will basically return the value
     * of $this->response with the desired type.
     *
     * @return string The XML content
     */
    public function executeOutput()
    {
        return $this->response;
    }
}