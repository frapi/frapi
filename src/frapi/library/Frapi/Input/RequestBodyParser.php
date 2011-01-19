<?php
/**
 * Request Body Parser class
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
 * This class attempts to parse JSON or XML from the request body.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 * @uses      Frapi_Error
 */
class Frapi_Input_RequestBodyParser
{
    /**
     * Static parsing of the request body
     *
     * This method extracts parameters from request body 
     * by parsing content and either throws an exception if
     * the body is invalid XML or returns the decoded array from XML.
     *
     * @throws Frapi_Error
     * @return array|null decoded parameters
     */
    public static function parse($format, $body=null)
    {
        switch(strtolower($format)) {
            case 'json':
                $jsonBody = json_decode($body, true);
                if(!is_null($jsonBody)) {
                    return $jsonBody;
                }
                break;
            case 'xml':
                if(!empty($body)) {
                    $parseResponse = Frapi_Input_XmlParser::arrayFromXml($body);
                    if ($parseResponse instanceof Frapi_Exception) {
                        throw new Frapi_Error('INVALID_REQUEST_BODY', $parseResponse->getMessage(), $parseResponse->getCode());
                    }
                    return $parseResponse;
                }
                break;
            default:
                break;
        }
    }
}