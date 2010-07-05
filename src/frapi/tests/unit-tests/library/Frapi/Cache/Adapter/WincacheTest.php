<?php

/**
 * Frapi_Cache_Adapter_Wincache test case.
 * 
 * @todo Someone using wincache will have to flesh this out.
 */
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
class Frapi_Cache_Adapter_WincacheTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Frapi_Cache_Adapter_Wincache
     */
    private $Frapi_Cache_Adapter_Wincache;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        if (!extension_loaded('WinCache')) {
            $this->markTestSkipped(
              'The WinCache extension is not available.'
            );
        }
        
        parent::setUp();
        
        // TODO Auto-generated WincacheTest::setUp()
        

        $this->Frapi_Cache_Adapter_Wincache = new Frapi_Cache_Adapter_Wincache(/* parameters */);
    
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated WincacheTest::tearDown()
        

        $this->Frapi_Cache_Adapter_Wincache = null;
        
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct ()
    {    // TODO Auto-generated constructor
    }

    /**
     * Tests Frapi_Cache_Adapter_Wincache->get()
     */
    public function testGet ()
    {
        // TODO Auto-generated WincacheTest->testGet()
        $this->markTestIncomplete(
            "get test not implemented");
        
        $this->Frapi_Cache_Adapter_Wincache->get(/* parameters */);
    
    }

    /**
     * Tests Frapi_Cache_Adapter_Wincache->add()
     */
    public function testAdd ()
    {
        // TODO Auto-generated WincacheTest->testAdd()
        $this->markTestIncomplete(
            "add test not implemented");
        
        $this->Frapi_Cache_Adapter_Wincache->add(/* parameters */);
    
    }

    /**
     * Tests Frapi_Cache_Adapter_Wincache->delete()
     */
    public function testDelete ()
    {
        // TODO Auto-generated WincacheTest->testDelete()
        $this->markTestIncomplete(
            "delete test not implemented");
        
        $this->Frapi_Cache_Adapter_Wincache->delete(/* parameters */);
    
    }
}