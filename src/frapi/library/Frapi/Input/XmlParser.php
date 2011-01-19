<?php
/**
 * XML Parser class
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
 * Parses incoming Xml into arrays using PHP's
 * built-in SimpleXML and SimpleXMLIterator
 *
 * This parser attempts to be document-structure-agnostic, handle numeric keys,
 * and typecast values when attributes are specified.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Input_XmlParser
{
    /**
     * The XML root information of the XML element
     * that has been parsed.
     *
     * @var string The root element name.
     */
    private static $_xmlRoot;
    
    /**
     * @var The response type from the 
     */
    private static $_responseType;

    /**
     * Create an Array from XML
     *
     * This method sets up the SimpleXMLIterator and starts the parsing
     * of an xml body to iterate through it and transform it into
     * an array that can be used by the developers.
     *
     * @param string $xml
     * @return array An array mapped to the passed xml
     */
    public static function arrayFromXml($xml)
    {
        // replace namespace defs
        $xml = str_replace('xmlns=', 'ns=', $xml);

        // catch libxml errors
        libxml_use_internal_errors(true);
        try {
            $iterator = new SimpleXMLIterator($xml);
        } catch(Exception $e) {
            $xmlErrors = libxml_get_errors();
             return new Frapi_Exception(
                     'Xml Parsing Failed', 
                     'INVALID_XML', 
                     400, 
                     'xml_parsing'
                     );
             libxml_clear_errors();
        }
        
        $xmlRoot = $iterator->getName();
        $type = $iterator->attributes()->type;

        // SimpleXML provides the root information on construct
        self::$_xmlRoot = $iterator->getName();
        self::$_responseType = $type;
        
        // return the mapped array with the root element as the header
        return array($xmlRoot => self::_iteratorToArray($iterator));
    }

    /**
     * Processes SimpleXMLIterator objects recursively
     *
     * This method receives an Iterator Object and 
     * processes the object recursively to process all the
     * attributes.
     *
     * @param object $iterator
     * @return array xml converted to array
     */
    private static function _iteratorToArray($iterator)
    {
        $xmlArray = array();
        $iterator->rewind();
        if (!$iterator->valid()) {
            return self::_typecastXmlValue($iterator);
        }
        
        while($iterator->valid()) {
            $value = null;
            $tmpArray = null;
            // get the attribute type string for use in conditions below
            $attributeType = (string) (isset($iterator->attributes()->coerced_type)) ?
                $iterator->attributes()->coerced_type :
                $iterator->attributes()->type;

            // process children recursively
            $key = $iterator->key();

            if ($iterator->hasChildren()) {
                // determines if the element is an array
                if(empty($attributeType)) {

                    $childKeys = array();

                    foreach($iterator->getChildren() as $name => $data) {
                        $childKeys[] = $name;
                    }
                    // if there is exactly 1 unique key in the children,
                    // and there are many, treat it as an array
                    if(count(array_unique($childKeys)) === 1 && count($childKeys) > 1) {
                        // adding coerced_type instead of type to work around a PHPUnit issue
                        $iterator->current()->addAttribute('coerced_type', 'array');
                    }
                }
                // return the child elements
                $value = self::_iteratorToArray($iterator->current());

                // if the element is an array type,
                // use numeric keys to allow multiple values
                if ($attributeType != 'array') {
                    $tmpArray[$key] = $value;
                }
            } else {
                // cast values according to attributes
                $tmpArray[$key] = self::_typecastXmlValue($iterator->current());
            }

            // set the output string
            //$output = isset($tmpArray[$key]) ? $tmpArray[$key] : $value;
            $output = isset($value) ? $value : $tmpArray[$key];

            // if the element was an array type, output to a numbered key
            // otherwise, use the element name
            if($attributeType == 'array') {
                    $xmlArray[] = $output;
            } else {
                $xmlArray[$key] = $output;
            }

            $iterator->next();
        }

        return $xmlArray;
    }

    /**
     * Type case XML values based on attributes
     *
     * This method typecasts the xml values based on the
     * attributes of the SimpleXMLElement Object passed 
     * to the method. 
     *
     * @param object $valueObj SimpleXMLElement
     * @return mixed value for placing into array
     */
    private static function _typecastXmlValue($valueObj)
    {
        // get the element attributes
        $attribs = $valueObj->attributes();
        // the element is null, so jump out here
        if (isset($attribs->nil) && $attribs->nil ||
           (isset($attribs->null) && $attribs->null) ||
           (string)$valueObj == 'null') 
        {
            return null;
        }
        // switch on the type attribute
        // switch works even if $attribs->type isn't set
        $type = isset($attribs->coerced_type) ? $attribs->coerced_type : $attribs->type;
        switch ($type) {
            case 'datetime':
                return self::_timestampToUTC((string) $valueObj);
                break;
            case 'integer':
                return (int) $valueObj;
                break;
            case 'boolean':
                $value =  (string) $valueObj;
                // look for a number inside the string
                if(is_numeric($value)) {
                    return (bool) $value;
                }
                
                return ($value == 'true');
                break;
            case 'array':
                return array();
            default:
                return (string) $valueObj;
        }
    }

    /**
     * Convert XML timestamps to DateTime
     *
     * This method receives a timestamp and attempts to 
     * convert it to a DateTime object using the DateTimeZone UTC.
     *
     * @param  string $timestamp
     * @return DateTime A DateTime object with the UTC timezone.
     */
    private static function _timestampToUTC($timestamp)
    {
        $tz = new DateTimeZone('UTC');
        $dateTime = new DateTime($timestamp, $tz);
        $dateTime->setTimezone($tz);
        return $dateTime;
    }
}