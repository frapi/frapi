<?php
/**
 * Test Case
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
 * This class tests the output of Request Body Parser
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi-tests
 */
 
/**
 * Frapi_Input_RequestBodyParser test case.
 */
class Frapi_Input_RequestBodyParserTest extends PHPUnit_Framework_TestCase
{
    function testJSONDecode()
    {
        $json = '{ "foo": { "bar":["a","b","c"] } }';
        $decoded = Frapi_Input_RequestBodyParser::parse('json', $json);
        $this->assertEquals(
                array('foo' =>
                        array('bar' =>
                                array(0 => 'a', 1=>'b', 2=>'c'))),
                $decoded,
                "Array structure should match JSON");
    }

    function testXMLDecode()
    {
        $xml = <<<XML
 <root>
    <foos>
        <foo>bar</foo>
        <foo>bat</foo>
    </foos>
</root>
XML;
        $array = Frapi_Input_RequestBodyParser::parse('XML', $xml);
        $this->assertEquals(
                array('root' =>
                    array('foos' =>
                            array(0 => 'bar', 1=> 'bat'))),
                $array, "Array structure should match XML");
    }

    /**
     * @expectedException Frapi_Error
     * @expectedExceptionMessage String could not be parsed as XML
     */
    function testInvalidXML()
    {
        $xml = <<<XML
adsfadsfadddd

XML;
         Frapi_Input_RequestBodyParser::parse('xml', $xml);
    }

    function testEmpty()
    {
        $this->assertNull(
                Frapi_Input_RequestBodyParser::parse('json', '')
                );
    }
}
?>
