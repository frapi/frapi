<?php

class Frapi_Controller_MainTest extends PHPUnit_Framework_TestCase
{

    protected $_controller;

    public function setUp()
    {
        $this->_controller = new MockFrapi_Controller_Main();
    }

    /**
     * @dataProvider contentTypeProvider
     */
    public function testSetInputFormat($mimeType, $format)
    {
        $_SERVER['CONTENT_TYPE'] = $mimeType;
        $this->_controller->setInputFormat();
        $this->assertEquals($format, $this->_controller->getInputFormat());

    }

    public function contentTypeProvider()
    {
        return array(
           array('application/json', 'json'),
           array('text/json', 'json'),
           array('application/xml', 'xml'),
           array('text/xml', 'xml'),
           array('text/html', null)
        );
    }
}