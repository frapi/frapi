<?php
/**
 * HTML Output
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
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Output_HTML_Exception extends Frapi_Output_Exception {}

/**
 * XML Output Class
 *
 * @package Frapi
 * @uses    Frapi_Output
 * @uses    Frapi_Output_Interface
 */
class Frapi_Output_HTML extends Frapi_Output implements Frapi_Output_Interface
{
    
    /**
     * XML Mime Type
     *
     * @var string
     */
    public $mimeType = 'text/html';
    
    /**
     * Populate the Output
     *
     * This method populates the $this->response
     * variable with the value returned from the
     * action.
     *
     * @param Mixed $response Most of the times an array but could be and stdClass
     * @param String $customTemplate The custom template file to use instead of the default one.
     *                        
     * @return Object $This object
     */
    public function populateOutput($data, $customTemplate = false)
    {
        $directory = CUSTOM_OUTPUT . DIRECTORY_SEPARATOR . 'html';
        
        $file      = $directory . DIRECTORY_SEPARATOR .
                     ucfirst(strtolower($this->action)) . '.html.tpl';

         if ($customTemplate !== false) {
             $file = $directory . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR .
                     $customTemplate . '.html.tpl';
         }
         
        $html       = '';

        if (file_exists($file)) {
            ob_start();
            include $file;
            $html = ob_get_contents();
            ob_end_clean();
            $this->response = $html;
            return $this;
            
        } elseif ($this->action == 'defaultError') {
            $directory = LIBRARY_OUTPUT . DIRECTORY_SEPARATOR . 'html';
            $file      = $directory . DIRECTORY_SEPARATOR . 'Defaulterror.html.tpl';
            ob_start();
            echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            include $file;
            $xml = ob_get_contents();
            ob_end_clean();
            
            $this->response = $xml;
            return $this;
        }

        throw new Frapi_Output_HTML_Exception(
            'If you want HTML, you need to create ' . ucfirst($this->action) . 
            '.html.tpl in the ' . CUSTOM_OUTPUT . '/html directory.',
            
            'MISSING_HTML_TEMPLATE'
        );
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