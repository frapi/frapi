<?php

class OutputJSONTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the output when response is empty.
     **/
    public function testEmptyResponse()
    {
        $outputJSON = new Frapi_Output_JSON();
        $outputJSON->populateOutput(null);
        $this->assertEquals('null', $outputJSON->executeOutput());
        $outputJSON->populateOutput(array());
        $this->assertEquals('[]', $outputJSON->executeOutput());
    }
}