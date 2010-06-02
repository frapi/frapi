<?php

/**
 * Frapi_Cache_Adapter_Apc test case.
 */
class Frapi_Cache_Adapter_ApcTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Frapi_Cache_Adapter_Apc
     */
    private $Frapi_Cache_Adapter_Apc;
    
    /**
     * ttl for acp_store($key, $value, $ttl)
     * 
     * @var int
     */
    private $_ttl = 900;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        $this->Frapi_Cache_Adapter_Apc = new Frapi_Cache_Adapter_Apc();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Frapi_Cache_Adapter_Apc = null;
        parent::tearDown();
    }
    
    /**
     * Provides data for test functions in k=>v format. 
     *
     * @return Array
     **/
    public function keysValuesProvider()
    {
        return array(
            array('test-key0', 'value'),
            array('test-key1', 'value1'),
            array('test-key0', 'value-changed'),
            array('test-array-num', array(1, 2, 3, 4, 5)),
            array('test-array-assoc', array('a'=>'c', 'c'=>'d', 'e'=>123)),
            array('test-array-assoc-nested', array('a'=>array('c'=>array('t'=>'a-c-t-nest')), 'c'=>'d', 'e'=>123))
        );
    }

    /**
     * Tests Frapi_Cache_Adapter_Apc->get()
     * 
     * @dataProvider keysValuesProvider
     */
    public function testGet ($key, $value)
    {
        apc_store($key, $value, $this->_ttl);
        $this->assertEquals($value, $this->Frapi_Cache_Adapter_Apc->get($key));
    }

    /**
     * Tests Frapi_Cache_Adapter_Apc->add()
     * 
     * @dataProvider keysValuesProvider
     */
    public function testAdd ($key, $value)
    {
        $this->Frapi_Cache_Adapter_Apc->add($key, $value);
        $this->assertEquals($value, apc_fetch($key));
    }

    /**
     * Tests Frapi_Cache_Adapter_Apc->delete()
     * 
     * @dataProvider keysValuesProvider
     */
    public function testDelete ($key, $value)
    {
        apc_store($key, $value, $this->_ttl);
        $this->Frapi_Cache_Adapter_Apc->delete($key);
        $this->assertEquals(false, apc_fetch($key));
    }

    /**
     * Tests Frapi_Cache_Adapter_Apc->undelete()
     */
    public function testUndelete ()
    {
        $this->markTestIncomplete("undelete test not implemented");
        $this->Frapi_Cache_Adapter_Apc->undelete(/* parameters */);
    }

}

