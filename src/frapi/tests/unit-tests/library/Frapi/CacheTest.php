<?php

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

