<?php

class Frapi_ExceptionTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * @var Frapi_Exception
     */
    private $_e;
    
    public function setUp() {
        $this->_e = new Frapi_Exception(
                "You shouldna been doin' that", 
                'Frapi_Exception_Whatever', 
                400, 
                'In yo face!'
            );
    }
    
    public function testGetDefaultErrorCode()
    {
        $e = new Frapi_Exception('I haz default error code?', 'Frapi_Exception');
        $this->assertEquals(400, $e->getCode());
    }
    
    public function test__toString() 
    {
        ob_start();
        echo $this->_e;
        $message = ob_get_clean();
        $this->assertEquals("You shouldna been doin' that", $message);
    }
    
    public function testGetErrorArray()
    {
        $errors = $this->_e->getErrorArray();
        
        $this->assertArrayHasKey('errors', $errors);
        $this->assertEquals(3, count($errors['errors'][0]));
        $this->assertEquals("You shouldna been doin' that", $errors['errors'][0]['message']);
        $this->assertEquals('Frapi_Exception_Whatever', $errors['errors'][0]['name']);
        $this->assertEquals('In yo face!', $errors['errors'][0]['at']);
    }
}