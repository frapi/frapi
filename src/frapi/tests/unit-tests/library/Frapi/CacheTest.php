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
 
/**
 * Frapi_Cache test case.
 */
 
class Frapi_CacheTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * Tests Frapi_Cache::getInstance() returns default adapter
     */
    public function testDefaultAdapterIsApcAdapter() {
        $adapter = Frapi_Cache::getInstance();
        $this->assertType('Frapi_Cache_Adapter_Apc', $adapter);
        $this->assertType('Frapi_Cache_Interface', $adapter);
    }

    /**
     * Tests Frapi_Cache::getInstance() returns apc adapter
     */
    public function testGetInstanceApcAdapter ()
    {
        $adapter = Frapi_Cache::getInstance('apc');
        $this->assertType('Frapi_Cache_Adapter_Apc', $adapter);
        $this->assertType('Frapi_Cache_Interface', $adapter);
    }
    
    /**
     * Tests Frapi_Cache::getInstance() returns wincache adapter
     */
    public function testGetInstanceWincacheAdapter ()
    {
        $adapter = Frapi_Cache::getInstance('wincache');
        $this->assertType('Frapi_Cache_Adapter_Wincache', $adapter);
        $this->assertType('Frapi_Cache_Interface', $adapter);
    }
    
    /**
     * Tests Frapi_Cache::getInstance() returns wincache adapter
     */
    public function testGetInstanceAdapterDoesNotExist ()
    {
        $this->setExpectedException('Frapi_Cache_Adapter_Exception', 'Frapi_Cache_Adapter_Wtf does not exist');
        $adapter = Frapi_Cache::getInstance('wtf');
    }

}

