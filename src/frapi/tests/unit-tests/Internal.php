<?php

require_once 'phpunit/Framework.php';
include "../library/Frapi/AllFiles.php";
//require '../library/Frapi/Exception.php';
require '../library/Frapi/Internal.php';

class InternalTest extends PHPUnit_Framework_TestCase
{
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
     * Test Internal class can init DB.
     *
     **/
    public function testInitDB()
    {
        $db = Frapi_Internal::getDB();
        $this->assertTrue($db instanceof PDO);
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
            apc_delete($arr[0]);
        }
    }
}