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