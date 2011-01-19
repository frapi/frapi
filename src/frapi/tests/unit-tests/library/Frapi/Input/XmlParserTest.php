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
 * This class tests the sanity of the XML Parser
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi-tests
 */

/**
 * Frapi_Input_XmlParser test case.
 */
class Frapi_Input_XmlParserTest extends PHPUnit_Framework_TestCase
{

    function testTypeCastIntegers()
    {
        $array = Frapi_Input_XmlParser::arrayFromXml('<root><foo type="integer">123</foo></root>');
        $this->assertEquals($array, array('root' => array('foo' => 123)));
    }

    function testNullOrEmptyString()
    {
        $xml = <<<XML
<root>
    <a_nil_value nil="true"></a_nil_value>
    <an_empty_string></an_empty_string>
    <a_null_string>null</a_null_string>
    <a_null_value null="true"></a_null_value>
</root>
XML;
        $array = Frapi_Input_XmlParser::arrayFromXml($xml);
        $this->assertEquals(
                array('root' => array(
                    'a_nil_value' => null,
                    'an_empty_string' => '',
                    'a_null_string' => null,
                    'a_null_value' => null)),
                $array);
    }

    function testTypeCastsDatetimes()
    {
        $xml = <<<XML
<root>
  <createdAt type="datetime">2010-12-01T07:38:48Z</createdAt>
</root>
XML;
        $array = Frapi_Input_XmlParser::arrayFromXml($xml);
        date_default_timezone_set('UTC');
        $dateTime = new DateTime('2010-12-01T07:38:48', new DateTimeZone('UTC'));
        $this->assertEquals(
                array('root' => array(
                    'createdAt' => $dateTime,
                    )),
                $array);
        $this->assertInstanceOf('DateTime', $array['root']['createdAt']);
    }

    function testTypeCastsDates()
    {
        $xml = <<<XML
<root>
  <someDate type="date">2010-12-01</someDate>
</root>
XML;
        $array = Frapi_Input_XmlParser::arrayFromXml($xml);
        date_default_timezone_set('UTC');
        $dateTime = new DateTime('2010-12-01', new DateTimeZone('UTC'));
        $this->assertEquals(array('root' => array('someDate' => $dateTime->format('Y-m-d'))), $array);
    }

    public function testXmlWithRootNamespaces()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns="http://custom.site.com/api/">
<foo>bar</foo>
</root>
XML;
        $array = Frapi_Input_XmlParser::arrayFromXml($xml);
        $this->assertEquals(array('root' => array('foo' => 'bar')), $array);
    }
    function testXmlWithNumberedArray()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
<foos>
    <foo>
        <bar>bat</bar>
    </foo>
    <foo>
        <bar>baz</bar>
    </foo>
    <foo>
        <bar>buz</bar>
    </foo>
</foos>
</root>
XML;

        $array = Frapi_Input_XmlParser::arrayFromXml($xml);
        $this->assertEquals(array(
            'root' => array(
                'foos' => array(
                    0 => array('bar' => 'bat'),
                    1 => array('bar' => 'baz'),
                    2 => array('bar' => 'buz')
                    )
                )
            ),
            $array);
    }

    function testXmlWithArrayAttribute()
    {
        //$this->markTestSkipped();
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
<foos type="array">
    <foo>
        <bar>bat</bar>
    </foo>
    <foo>
        <bar>baz</bar>
    </foo>
    <foo>
        <bar>buz</bar>
    </foo>
</foos>
</root>
XML;

        $array = Frapi_Input_XmlParser::arrayFromXml($xml);
        $this->assertEquals(array('root' => array(
                'foos' => array(
                            0 => array('bar' => 'bat'),
                            1 => array('bar' => 'baz'),
                            2 => array('bar' => 'buz')
                        )
                    )
                ),
                $array
        );
    }


    function testReturnsBoolean()
    {
        $xml = <<<XML
<root>
  <castedTrue type="boolean">true</castedTrue>
  <castedOne type="boolean">1</castedOne>
  <castedFalse type="boolean">false</castedFalse>
  <castedAnything type="boolean">anything</castedAnything>
  <uncastedTrue>true</uncastedTrue>
</root>
XML;
         $array = Frapi_Input_XmlParser::arrayFromXml($xml);
         $this->assertEquals(
            array('root' =>
              array('castedTrue' => true,
                    'castedOne' => true,
                    'castedFalse' => false,
                    'castedAnything' => false,
                    'uncastedTrue' => 'true')
        ), $array);

    }


    function testEmptyArrayAndNestedElements()
    {
        $xml = <<<XML
<root>
  <nestedValues>
    <value>1</value>
  </nestedValues>
  <noValues type="array"/>
</root>
XML;

         $array = Frapi_Input_XmlParser::arrayFromXml($xml);
         $this->assertEquals(
              array('root' => array(
                  'nestedValues' => array('value' => 1),
                  'noValues' => array(),
                )
              ), $array);
    }

    function testParsingNullEqualsTrueAfterArray()
    {
        $xml = <<<XML
<root>
  <foos>
    <foo>bar</foo>
  </foos>
  <blank null="true" />
</root>
XML;
        $array = Frapi_Input_XmlParser::arrayFromXml($xml);
        $this->assertEquals(null, $array['root']['blank']);
    }

    function testXmlWithNestedElements()
    {
        $xml = <<<XML
<root>
  <el>
    <el2>
      <nest1>test</nest1>
        <nest2>test2</nest2>
        <nest3>test3</nest3>
    </el2>
  </el>
</root>
XML;
        $array = Frapi_Input_XmlParser::arrayFromXml($xml);
        $this->assertEquals(array(
            'root' => array(
                'el' => array(
                    'el2' => array(
                        'nest1' => 'test',
                        'nest2' => 'test2',
                        'nest3' => 'test3'
                    )
                )
            )
        ), $array);
    }
}
