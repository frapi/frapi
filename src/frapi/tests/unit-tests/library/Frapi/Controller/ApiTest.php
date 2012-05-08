<?php

class Frapi_Controller_ApiTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Frapi_Internal::setCached('Output.mimeMaps', array (
            'application/xml' => 'xml',
            'text/xml' => 'xml',
            'application/json' => 'json',
            'text/json' => 'json',
            'text/html' => 'html',
            'text/plain' => 'json',
            'text/javascript' => 'js',
            'text/php-printr' => 'printr',
            'application/vnd.test.:format' => ':format',
            'application/vnd.test.:version.:format' => ':format',
            'application/vnd.test.:version+json' => 'json',
            'text/csv' => 'csv',
        ));
    }

    /**
     * test various Accept header values, passed via acceptProvider
     *
     * @dataProvider acceptProvider
     */
    public function testDetectFormats($accepts, $outputFormat, $mimetype, $params)
    {
        $_SERVER['HTTP_ACCEPT'] = $accepts;

        $controller = new MockFrapi_Controller_Api();
        $detectedOutputType = $controller->detectOutputFormat();

        $this->assertEquals($mimetype, strtolower($detectedOutputType['mimetype']), "Mimetypes do not match");
        $this->assertEquals($outputFormat, strtolower($detectedOutputType['outputFormat']), "Output Formats do not match");
        $this->assertEquals($params, $detectedOutputType['params'], "Params does not match");
    }

    /**
     * test various REQUEST_URI extensions, passed via uriProvider
     *
     * @dataProvider uriProvider
     */
    public function testDetectExtensions($uri, $outputFormat, $mimetype)
    {
        $_SERVER['REQUEST_URI'] = $uri;

        $controller = new MockFrapi_Controller_Api();

        $detectedOutputType = $controller->detectOutputFormat();
        $this->assertEquals($mimetype, strtolower($detectedOutputType['mimetype']), "Mimetypes do not match");
        $this->assertEquals($outputFormat, strtolower($detectedOutputType['outputFormat']), "Output Formats do not match");
    }

    /**
     * @return array
     */
    public function acceptProvider()
    {
        return array(
            /* Basic mimetypes */
            array('application/xml', 'xml', 'application/xml', array()),
            array('text/xml', 'xml', 'text/xml', array()),
            array('application/json', 'json', 'application/json', array()),
            array('text/json', 'json', 'text/json', array()),
            array('text/html', 'html', 'text/html', array()),
            array('text/plain', 'json', 'text/plain', array()),
            array('text/javascript', 'js', 'text/javascript', array()),
            array('text/php-printr', 'printr', 'text/php-printr', array()),

            /* Test q-values */
            array('text/html;q=0.9,text/json,application/json', 'json', 'text/json', array()),
            array('text/php-printr,text/html;q=0.9,application/json;q=0.8', 'printr', 'text/php-printr', array()),

            /* Test bad mimetypes return default */
            array('text/fake', 'json', 'application/json', array()), /* @see MockFrapi_Controller_Main::getDefaultFormatFromConfiguration() */

            /* Test q-values with bad mimetypes */
            array('text/plain;q=0.9,text/fake', 'json', 'text/plain', array('q' => '0.9')),
            array('text/fake,text/plain', 'json', 'text/plain', array()),

            /* Make sure only the winning mimetype's params are returned */
            array('text/json,text/html;q=1', 'json', 'text/json', array()),

            /* Test mimetypes with params */
            array('text/json;version=1,text/html;version=2', 'json', 'text/json', array('version' => 1)),
            array('text/json;version=1;foo=bar,text/html;version=2', 'json', 'text/json', array('version' => 1, 'foo' => 'bar')),
            array('text/json;version=1;foo=bar', 'json', 'text/json', array('version' => '1', 'foo' => 'bar')),

            /* Test simple custom mimetype */
            array('text/csv', 'csv', 'text/csv', array()),
            array('text/csv;q=0.9,text/html', 'html', 'text/html', array()),

            /* Test simple custom mimetype with params */
            array('text/csv;q=0.9;version=1', 'csv', 'text/csv', array('q' => '0.9', 'version' => '1')),
            array('text/csv;q=0.9;version=1,text/html;version=2', 'html', 'text/html', array('version' => '2')),
            array('text/html;q=0.9,text/csv;version=1', 'csv', 'text/csv', array('version' => '1')),

            /* Test custom mimetypes with placeholders and/or params */
            array('application/vnd.test.json', 'json', 'application/vnd.test.json', array('format' => 'json')),
            array('application/vnd.test.xml', 'xml', 'application/vnd.test.xml', array('format' => 'xml')),
            array('application/vnd.test.json;version=1;foo=bar, application/json;q=0.9', 'json', 'application/vnd.test.json', array('format' => 'json', 'version' => '1', 'foo' => 'bar')),
            array('application/vnd.test.v1+json, application/json;q=0.9', 'json', 'application/vnd.test.v1+json', array('version' => 'v1')),

            /* Test custom mimetypes with multiple placeholders */
            array('application/vnd.test.v1.json;foo=bar, application/json;q=0.9', 'json', 'application/vnd.test.v1.json', array('format' => 'json', 'version' => 'v1', 'foo' => 'bar')),
            array('application/vnd.test.v1.json;q=0.9;foo=bar, application/vnd.test.json', 'json', 'application/vnd.test.json', array('format' => 'json')),

            /* Test Bad Inputs */
            array(null, 'json', 'application/json', array()),
            array("&bad mimetype&", 'json', 'application/json', array()),
        );
    }

    /**
     * @return array
     */
    public function uriProvider()
    {
        return array(
            array('/foo.json', 'json', 'application/json'),
            array('/foo.xml', 'xml', 'application/xml'),
            array('/foo.html', 'html', 'text/html'),
            array('/foo.js', 'js', 'text/javascript'),
            array('/foo.printr', 'printr', 'text/php-printr'),

            /* Test Multi-segment paths */
            array('/foo/bar.json', 'json', 'application/json'),
        );
    }
}