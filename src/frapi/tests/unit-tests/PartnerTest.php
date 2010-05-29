<?php

require_once 'phpunit/Framework.php';
require '../library/Frapi/Model/Partner.php';

class PartnerTest extends PHPUnit_Framework_TestCase
{
    //Not sure what tests to include here!
    //Partner Model is purely database access.
    public function testClassExists()
    {
        if (!class_exists('Frapi_Model_Partner')) {
            $this->fail('Partner class not defined.');
        }
    }
}