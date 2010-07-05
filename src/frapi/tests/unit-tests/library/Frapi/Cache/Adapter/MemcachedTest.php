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
 * Frapi_Cache_Adapter_Memcached test case.
 */
class Frapi_Cache_Adapter_MemcachedTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Frapi_Cache_Adapter_Memcached
     */
    private $Frapi_Cache_Adapter_Memcached;
    
    /**
     * Memcached object
     *
     * @var Memcached $cache  The memcached object.
     */
    private $cache;
    
    /**
     * ttl for acp_store($key, $value, $ttl)
     * 
     * @var int
     */
    private $_ttl = 900;


    /**
     * Constructs the test case.
     */
    public function __construct ()
    {
    }

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        if (!extension_loaded('Memcached')) {
            $this->markTestSkipped(
              'The Memcached extension is not available.'
            );
        }
        
        parent::setUp();
        $this->cache = new Memcached();
        $this->cache->addServer('127.0.0.1', '11211');
        
        $this->Frapi_Cache_Adapter_Memcached = new Frapi_Cache_Adapter_Memcached();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Frapi_Cache_Adapter_Memcached = null;
        $this->cache = null;
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
     * Tests Frapi_Cache_Adapter_Memcached->get()
     * 
     * @dataProvider keysValuesProvider
     */
    public function testGet ($key, $value)
    {
        $this->cache->set($key, $value, $this->_ttl);
        $this->assertEquals($value, $this->Frapi_Cache_Adapter_Apc->get($key));
    }

    /**
     * Tests Frapi_Cache_Adapter_Memcached->add()
     * 
     * @dataProvider keysValuesProvider
     */
    public function testAdd ($key, $value)
    {
        $this->Frapi_Cache_Adapter_Apc->add($key, $value);
        $this->assertEquals($value, $this->cache->get($key));
    }

    /**
     * Tests Frapi_Cache_Adapter_Apc->delete()
     * 
     * @dataProvider keysValuesProvider
     */
    public function testDelete ($key, $value)
    {
        $this->cache->add($key, $value, $this->_ttl);
        $this->Frapi_Cache_Adapter_Apc->delete($key);
        $this->assertEquals(false, $this->cache->get($key));
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

