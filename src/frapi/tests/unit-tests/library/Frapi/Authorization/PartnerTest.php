<?php

/**
 * Frapi_Authorization_Partner test case.
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
class Frapi_Authorization_PartnerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Frapi_Authorization_Partner
     */
    private $Frapi_Authorization_Partner;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
        // TODO Auto-generated Frapi_Authorization_PartnerTest::setUp()
        

        $this->Frapi_Authorization_Partner = new Frapi_Authorization_Partner(/* parameters */);
    
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated Frapi_Authorization_PartnerTest::tearDown()
        

        $this->Frapi_Authorization_Partner = null;
        
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct ()
    {    // TODO Auto-generated constructor
    }

    /**
     * Tests Frapi_Authorization_Partner->authorize()
     */
    public function testAuthorize ()
    {
        // TODO Auto-generated Frapi_Authorization_PartnerTest->testAuthorize()
        $this->markTestIncomplete(
        "authorize test not implemented");
        
        $this->Frapi_Authorization_Partner->authorize(/* parameters */);
    
    }

}

