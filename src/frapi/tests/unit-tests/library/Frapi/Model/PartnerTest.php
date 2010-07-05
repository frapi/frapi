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
class Frapi_Model_PartnerTest extends PHPUnit_Framework_TestCase
{
    
    public function setUp() 
    {
        $_SERVER['HTTP_HOST'] = 'testing';
    }
    
    public function testIsPartnerExpectTrue() 
    {
        $partner = array(
            'test@getfrapi.com' => array(
                'email'   => 'test@getfrapi.com',
                'api_key' => 'e5b9e917648c57a978ba633095cb7a12fb0a647e'
            )
        );
            
        Frapi_Internal::setCached('Partners.emails-keys', $partner);
        
        $partner = Frapi_Model_Partner::isPartner('test@getfrapi.com', 'e5b9e917648c57a978ba633095cb7a12fb0a647e');
        $this->assertTrue($partner);
        
        Frapi_Internal::deleteCached('Partners.emails-keys');
    }
    
    public function testIsPartnerExpectFalse() {
        $partner = Frapi_Model_Partner::isPartner('email', 'key');
        $this->assertFalse($partner);
    }
}