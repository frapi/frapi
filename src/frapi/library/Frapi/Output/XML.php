<?php
/**
 * XML Output
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
 * @author  David Doran <david.doran@echolibre.com>
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Output_XML_Exception extends Frapi_Output_Exception {}

/**
 * XML Output Class
 *
 * @package Frapi
 * @uses    Frapi_Output
 * @uses    Frapi_Output_Interface
 */
class Frapi_Output_XML extends Frapi_Output implements Frapi_Output_Interface
{
    /**
     * Type Hinting
     *
     * Whether to set @type attribute on nodes.
     *
     * @var string
     */
    private $_typeHinting = false;
    
    /**
     * Numeric Key
     *
     * Whether to use <numeric-key>.
     *
     * @var boolean
     */
    private $_numericKey = false;

    /**
     * XML Mime Type
     *
     * @var string
     */
    public $mimeType = 'application/xml';

    /**
     * Populate the Output
     *
     * This method populates the $this->response
     * variable with the value returned from the
     * action.
     *
     * @param Mixed  $response Most of the times an array but could be and stdClass
     * @param String $customTemplate The custom template file to use instead of the default one.
     *
     * @return Object $This object
     */
    public function populateOutput($data, $customTemplate = false)
    {
        $directory = CUSTOM_OUTPUT . DIRECTORY_SEPARATOR . 'xml';

        $file = $directory . DIRECTORY_SEPARATOR .
                ucfirst(strtolower($this->action)) . '.xml.tpl';

        if ($customTemplate !== false) {
            $file = $directory . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR .
                    $customTemplate . '.xml.tpl';
        }

        $xml = '';

        $print = hash('md5', json_encode(
            $data + array('__action__name' => $this->action)
        ));

        if ($response = Frapi_Internal::getCached($print)) {
            $this->response = json_decode($response);
        } elseif (file_exists($file)) {
            ob_start();
            echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            include $file;
            $xml = ob_get_contents();
            ob_end_clean();
            $this->response = $xml;
            Frapi_Internal::setCached($print, json_encode($xml));

        } elseif ($this->action == 'defaultError') {
            $directory = LIBRARY_OUTPUT . DIRECTORY_SEPARATOR . 'xml';
            $file      = $directory . DIRECTORY_SEPARATOR . 'Defaulterror.xml.tpl';
            ob_start();
            echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            include $file;
            $xml = ob_get_contents();
            ob_end_clean();

            $this->response = $xml;
            Frapi_Internal::setCached($print, json_encode($xml));
        } else {
            $this->response = $this->_generateXML($data);
        }

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

    /**
     * Set type hinting on/off.
     *
     * @param Boolean $set_typeHinting_on Whether to turn hinting on or off.
     *
     * @return void
     */
    public function setTypeHinting($set_typeHinting_on)
    {
        $this->_typeHinting = false;
        if ($set_typeHinting_on) {
            $this->_typeHinting = true;
        }
    }
    
	/**
     * Set Numeric Key use on/off.
     *
     * @param Boolean $numericKey Whether to turn numeric key on or off.
     *
     * @return void
     */
    public function setNumericKey($numericKey)
    {
        $this->_numericKey = (boolean) $numericKey;
    }

    /**
     * Generate XML representation of PHP Array
     *
     * Using a named hierarchy, the XML can encode
     * associative arrays, objects, numeric arrays (using <key> nodes)
     * and scalars using PCDATA.
     *
     * @param Array $response Response array to be serialized.
     *
     * @return String XML Content
     */
    private function _generateXML($response)
    {
        //Create XMLWriter object
        $writer = new XMLWriter();
        //We want to write to memory
        if ($writer->openMemory()) {
            //Start document and set indent
            $writer->startDocument('1.0');
            $writer->setIndent(4);

            //Start main response element
            $writer->startElement('response');

            //First call to _generateItemXML which generates the
            //XML for a single variable, array entry etc.
            //This is recursive, we start with the response array.
            if (is_scalar($response)) {
                $response = array();
            }

            $this->_generateItemXML($writer, $response);

            //Close response element and end document
            $writer->endElement();
            $writer->endDocument();

            return $writer->outputMemory();
        } else {
            //@Throw XML openMemory Exception
        }
    }

    /**
     * Generate XML for a single variable item
     *
     * Scalars will become simply VALUE, numeric arrays
     * will become variously <numeric-key> or <VALUE>
     * and assoc arrays will become <$NAME>$VALUE1</$NAME><$NAME>$VALUE2</$NAME>.
     *
     * @param XMLWriter $writer   The XMLWriter object to be written to.
     * @param Mixed     $variable The variable to be serialized.
     *
     * @return void No return -- function operates on XML writer.
     */
    private function _generateItemXML($writer, $variable)
    {
        //If type hinting is on, set type.
        if ($this->_typeHinting) {
            $writer->writeAttribute('type', gettype($variable));
        }

        //Now, handle this item's content and sub-elements.
        if (is_array($variable)) {
            if ($this->_arrayIsAssoc($variable)) {
                foreach ($variable as $key=>$value) {
                    $this->_generateKeyValueXML($writer, $key, $value);
                }
            } else {
                foreach ($variable as $value) {
                    $this->_generateKeyValueXML($writer, null, $value);
                }
            }
        } else {
            $writer->text($variable);
        }
    }

    /**
     * Generate the XML for a known key and value
     *
     * Abstracts handling for numeric, self-indexing numeric and assoc.
     *
     * @param XMLWriter $writer The XMLWriter object to be written to.
     * @param Mixed     $key    The key for this element.
     * @param Mixed     $value  The value for this element.
     *
     * @return void No return -- function operates on XML writer.
     */
    private function _generateKeyValueXML($writer, $key, $value)
    {
    	$doEnd = true;
        //If key is numeric and value is string, make empty element: <VALUESTRING />
        if ((is_numeric($key) || is_null($key))
            && is_string($value)
            && preg_match('/^[_A-Za-z\:]{1}[\-\.0-9a-zA-Z]*$/', $value)
        ) {
            $key = $value;
            $value = null;
        }

        /**
         * Algo for handling keys and values:
         * 1. If key is not a normal (valid XML) element name write <numeric-key>
         *    1.1 If key is null then parent array was numeric. (self-indexing)
         * 2. Else, create container
         * 	  2.1 If value is non assoc array, non empty. Add each element with the same key.
         *    2.2 Else open XML element name using key 
         *    2.3 Else IF value is null, then create empty element.
         *
         */
        if (is_numeric($key) or is_null($key)) {
            $writer->startElement('numeric-key');
            if (!is_null($key)) {
                $writer->writeAttribute('key', $key);
            }

            $this->_generateItemXML($writer, $value);
        } else {
        	if (!$this->_numericKey && is_array($value) && count($value) > 0 && !$this->_arrayIsAssoc($value)) {
        		foreach($value as $v) {
	        		try {
		                $writer->startElement($key);
		            } catch (Exception $e) {
		                throw new Frapi_Output_XML_Exception('Invalid XML element name, cannot create element.', 'Frapi_Output_XML_Exception');
		            }
        			$this->_generateItemXML($writer, $v);
        			$writer->endElement();
        		}
        		$doEnd = false;
        	} else {
	            try {
	                $writer->startElement($key);
	            } catch (Exception $e) {
	                throw new Frapi_Output_XML_Exception('Invalid XML element name, cannot create element.', 'Frapi_Output_XML_Exception');
	            }
	
	            if (!is_null($value)) {
	                $this->_generateItemXML($writer, $value);
	            }
        	}
        }
		if ($doEnd) {
        	$writer->endElement();
		}
    }

    /**
     * Utility is array assoc
     *
     * Utility function: Check whether array is associative.
     * There is only one set of keys for a given size array that
     * makes the array numeric, check for those!
     *
     * @param Array $array Array to check.
     *
     * @return Boolean Array is associative?
     */
    private function _arrayIsAssoc($array)
    {
        return !ctype_digit( implode('', array_keys($array) ) );
    }
}

