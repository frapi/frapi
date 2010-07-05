<?php
/**
 * Test Case
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
 * This class contains the rules about actions/outputs
 *
 * It mostly contains methods that are there to validate
 * the types and actions requested.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi-tests
 */
class Frapi_Output_XMLTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the XML output of an empty response from action.
     **/
    public function testEmptyResponse()
    {
        $outputXML = new Frapi_Output_XML();
        $outputXML->populateOutput(null);
        $this->assertXMLStringEqualsXMLString('<response />', $outputXML->executeOutput());
        $outputXML->populateOutput(array());
        $this->assertXMLStringEqualsXMLString('<response />', $outputXML->executeOutput());
    }
    
    /**
     * Test scalars are rejected by XML output generator.
     * @dataProvider scalarProvider
     **/
    public function testScalarsRejected($scalar)
    {
        $outputXML = new Frapi_Output_XML();
        $outputXML->populateOutput($scalar);
        $this->assertXMLStringEqualsXMLString('<response />', $outputXML->executeOutput());
    }
    
    /**
     * Test that XML generated from response matches expected.
     *
     * @dataProvider responseArrayProvider
     **/
    public function testExpectedXML($response, $expectedXML)
    {
        $outputXML = new Frapi_Output_XML();
        
        try {
            $outputXML->populateOutput($response);
        } catch (Frapi_Output_XML_Exception $e) {
            if ($expectedXML !== false) {
                $this->fail('Expected Frapi_Output_XML_Exception because of invalid element names.');
            }
        }
        if ($expectedXML !== false) {
            $generatedXML = $outputXML->executeOutput();
            $this->assertXMLStringEqualsXMLString($expectedXML, $generatedXML);
            try {
                simplexml_load_string($generatedXML);
            } catch (Exception $e) {
                $this->fail('Invalid XML generated.');
            }
        }
    }
    
    /**
     * Scalar Provider
     **/
    public function scalarProvider()
    {
        return array(
            array(1),
            array(123.456),
            array(-123.456),
            array(false),
            array(true),
            array("Double-Quoted String"),
            array("Single-Quoted String")
            );
    }
    
    /**
     * Response arrays for testing
     **/
    public function responseArrayProvider()
    {
        return array(
            //Basic numeric array
            array(
                array(1),
                '<response><numeric-key>1</numeric-key></response>'
                ),
            //Basic numeric array, many elements
            array(
                array(1, 2, 3, 4),
                '<response><numeric-key>1</numeric-key><numeric-key>2</numeric-key><numeric-key>3</numeric-key><numeric-key>4</numeric-key></response>'
                ),
            //Associative Array
            array(
                array('a'=>'b', 'b'=>'c', 'c'=>'d'),
                '<response><a>b</a><b>c</b><c>d</c></response>'
                ),
            //Mixed numeric and associative array
            //Entries with numeric keys and string values are converted to empty elements
            array(
                array('a', 'b', 'c'=>'d', 'd'=>'e'),
                '<response><a/><b/><c>d</c><d>e</d></response>'
                ),
            //Values that would create invalid XML elements are converted to numeric-key's. 
            array(
                array('numeric-key invalid value', 'assoc'=>'value'),
                '<response><numeric-key key="0">numeric-key invalid value</numeric-key><assoc>value</assoc></response>'
                ),
            //Multiple-level hierarchical array
            array(
                array('a' => array(1, 2, array('c', 'd', 'e'))),
                '<response>
                    <a>
                        <numeric-key>1</numeric-key>
                        <numeric-key>2</numeric-key>
                        <numeric-key>
                            <c/>
                            <d/>
                            <e/>
                        </numeric-key>
                    </a>
                </response>'
                ),
            //Multiple-level example checking invalid elements aren't created.
            array(
                array('a_b' => array('d', 'x', '_ssl'=>array('-_invalid_nodeName', '123_invalidNodeName'))),
                '<response>
                    <a_b>
                        <d/>
                        <x/>
                        <_ssl>
                            <numeric-key>-_invalid_nodeName</numeric-key>
                            <numeric-key>123_invalidNodeName</numeric-key>
                        </_ssl>
                    </a_b>
                </response>'
                ),
            //Multiple-level example; checking invalid elements aren't created.
            array(
                array('Key Name' => 'Key Value'),
                false
                )
            );
    }
}