<?php

class Frapi_Output_XMLCustomTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the XML output of an empty response from action.
     **/
    public function testCustomTemplate()
    {
        $outputXML = new Frapi_Output_XML();
        $outputXML->populateOutput(null, 'CustomTesting2');
        $this->assertXMLStringEqualsXMLString(
            '<response><custom /></response>', $outputXML->executeOutput()
        );
    }
}