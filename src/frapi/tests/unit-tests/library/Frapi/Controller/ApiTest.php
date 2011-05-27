<?php

class Frapi_Controller_ApiTest extends PHPUnit_Framework_TestCase
{

    protected $_controller;

    public function setUp()
    {
        $this->_controller = new MockFrapi_Controller_Api();
    }
    /**
     * @dataProvider acceptProvider
     */
    public function testDetectMimeType($mimeType, $dataType)
    {
        $_SERVER['HTTP_ACCEPT'] = $mimeType;
        $detectedOutputType = $this->_controller->detectAndSetMimeType();
        $this->assertEquals($dataType, strtolower($detectedOutputType['outputFormat']));
    }

    public function acceptProvider()
    {
        return array(
            array('application/xml', 'xml'),
            array('text/xml', 'xml'),
            array('application/json', 'json'),
            array('text/json', 'json'),
            array('text/html', 'html'),
            array('text/plain', 'json'),
            array('text/javascript', 'js'),
            array('text/php-printr', 'printr'),
            array('text/html;q=0.9,text/json,application/json', 'json'),
            array('text/php-printr,text/html;q=0.9,application/json;q=0.8', 'printr'),
            array('text/plain;q=0.9,text/fake', 'json'),
            array('text/plain,text/fake', 'json'),
            array('text/fake', false)
        );
    }
}