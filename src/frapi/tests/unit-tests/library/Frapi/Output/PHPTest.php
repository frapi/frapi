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
class Frapi_Output_PHPTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the output when response is empty.
     **/
    public function testEmptyResponse()
    {
        $outputPHP = new Frapi_Output_PHP();
        $outputPHP->populateOutput(null);
        $this->assertEquals('N;', $outputPHP->executeOutput());
        $outputPHP->populateOutput(array());
        $this->assertEquals('a:0:{}', $outputPHP->executeOutput());
    }
}
