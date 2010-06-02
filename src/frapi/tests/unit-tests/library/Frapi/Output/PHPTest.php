<?php

class Frapi_Output_PHPTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the output when response is empty.
     **/
    public function testEmptyResponse()
    {
        $outputPHP = new Frapi_Output_PHP();
        $outputPHP->populateOutput(null);
        $this->assertEquals('N;', $outputPHP->executeOutput());
        $outputPHP->populateOutput(array());
        $this->assertEquals('a:0:{}', $outputPHP->executeOutput());
    }
}
