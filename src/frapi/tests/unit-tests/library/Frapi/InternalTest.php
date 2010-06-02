<?php

class Frapi_InternalTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * @var mixed
     */
    private $_cache;
    
    public function setUp() 
    {
        $_SERVER['HTTP_HOST'] = 'testing';    
    }
    
    /**
     * Test cache values do not exist, i.e., they were torn down!!
     *
     * @dataProvider keysValuesProvider
     **/
    public function testCacheEmpty($key, $value)
    {
        $this->assertEquals(false, Frapi_Internal::getCached($key));
    }
    
    /**
     * Test cache stores and retrieves items.
     *
     * @dataProvider keysValuesProvider
     **/
    public function testCacheStoreAndRetrieve($key, $value)
    {
        Frapi_Internal::setCached($key, $value);
        $this->assertEquals($value, Frapi_Internal::getCached($key));
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
     * Tear Down - delete APC keys used in tests.
     *
     * @return void
     **/
    protected function tearDown()
    {
        $kvs = $this->keysValuesProvider();
        foreach ($kvs as $arr) {
            Frapi_Internal::deleteCached($arr[0]);
//            apc_delete($arr[0]);
        }
    }
}
