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
 * This class contains the rules about errors
 *
 * It mostly contains methods that are there to validate
 * the errors.
 *
 * @license   New BSD
 * @package   frapi-tests
 */
class Frapi_ErrorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Frapi_Error
     */
    private $_e;

    public function setUp()
    {
        $this->_e = new Frapi_Error(
                'Frapi_Error',
                'Frapi Error Message',
                400,
                'Frapi Error'
        );
    }

    public function testErrorNameIsException()
    {
        $e = new Frapi_Error(new Exception('Exception thrown', '99'));
        $error = $e->getErrorArray();
        $this->assertEquals(400, $e->getCode());
        $this->assertEquals('Bad Request', $e->getReasonPhrase());
        $this->assertEquals('Exception thrown', $error['errors'][0]['message']);
        $this->assertEquals(99, $error['errors'][0]['name']);
        $this->assertEquals('', $error['errors'][0]['at']);
    }

    public function testLoadErrorFromName()
    {
        $this->markTestIncomplete('Can we guarantee our default errors will always be there');
    }

    public function testReasonPhraseSetByErrorMsg()
    {
        $e = new Frapi_Error('SOME_ERROR', 'Error Message', 666);
        $this->assertEquals('Error Message', $e->getReasonPhrase());
    }

    public function testReasonPhraseSetByStandardCode()
    {
        $e = new Frapi_Error('SOME_ERROR', 'Error Message', 400);
        $this->assertEquals('Bad Request', $e->getReasonPhrase());
    }

    public function testErrorHandler()
    {
        try {
            Frapi_Error::errorHandler(E_ERROR, 'This is a PHP error', 'ErrorFile.php', '99');
        } catch (Frapi_Error $e) {
            $error = $e->getErrorArray();
        }
        $this->assertEquals(400, $e->getCode());
        $this->assertEquals('Bad Request', $e->getReasonPhrase());
        $this->assertEquals('This is a PHP error (Error Number: 1), (File: ErrorFile.php at line 99)', $error['errors'][0]['message']);
        $this->assertEquals('PHP Fatal error', $error['errors'][0]['name']);
        $this->assertEquals('', $error['errors'][0]['at']);
    }

    public function testCallStaticMesage()
    {
        $this->markTestIncomplete('Need to guarantee we have default errors. Also, is this even used? I cant find it in code.');
    }
}