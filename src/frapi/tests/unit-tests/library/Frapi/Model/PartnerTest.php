<?php

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