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
class Frapi_ActionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Frapi_Action
     */
    private $action;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        parent::tearDown();
    }

    /**
     * Tests Frapi_Action::getInstance()
     */
    public function testGetInstanceActionExists ()
    {
        $action = Frapi_Action::getInstance('Testing1');
        $this->assertNotNull($action);
        $this->assertType('Frapi_Action', $action);
        $this->assertEquals('Action_Testing1', get_class($action));
    }
    
    public function testGetInstanceActionDoesNotExist()
    {
        $this->setExpectedException('Frapi_Action_Exception');
        $action = Frapi_Action::getInstance('ActionDoesNotExist');
    }

    /**
     * Tests Frapi_Action->setActionFiles()
     */
    public function testSetActionFiles ()
    {
        // TODO Auto-generated Frapi_ActionTest->testSetActionFiles()
        $this->markTestIncomplete("setActionFiles test not implemented");
        
        $this->action->setActionFiles(/* parameters */);
    
    }

    /**
     * Tests Frapi_Action->setActionParams()
     */
    public function testSetActionParams ()
    {
        $actionParams = array('one' => 'one', 1 => 'two');
        
        $action = Frapi_Action::getInstance('Testing1');
        $action->setActionParams($actionParams);
        $params = $action->getParams();
        
        $this->assertEquals($actionParams, $params);
    }
    
    /**
     * Tests Frapi_Action->executeGet()
     */
    public function testExecuteGet ()
    {
        // TODO Auto-generated Frapi_ActionTest->testExecuteGet()
        $this->markTestIncomplete(
        "executeGet test not implemented");
        
        $this->action->executeGet(/* parameters */);
    
    }

    /**
     * Tests Frapi_Action->executePut()
     */
    public function testExecutePut ()
    {
        // TODO Auto-generated Frapi_ActionTest->testExecutePut()
        $this->markTestIncomplete(
        "executePut test not implemented");
        
        $this->action->executePut(/* parameters */);
    
    }

    /**
     * Tests Frapi_Action->executePost()
     */
    public function testExecutePost ()
    {
        // TODO Auto-generated Frapi_ActionTest->testExecutePost()
        $this->markTestIncomplete(
        "executePost test not implemented");
        
        $this->action->executePost(/* parameters */);
    
    }

    /**
     * Tests Frapi_Action->executeDelete()
     */
    public function testExecuteDelete ()
    {
        // TODO Auto-generated Frapi_ActionTest->testExecuteDelete()
        $this->markTestIncomplete(
        "executeDelete test not implemented");
        
        $this->action->executeDelete(/* parameters */);
    
    }

    /**
     * Tests Frapi_Action->executeHead()
     */
    public function testExecuteHead ()
    {
        // TODO Auto-generated Frapi_ActionTest->testExecuteHead()
        $this->markTestIncomplete(
        "executeHead test not implemented");
        
        $this->action->executeHead(/* parameters */);
    
    }

    /**
     * Tests Frapi_Action->getParams()
     */
    public function testGetParams ()
    {
        // TODO Auto-generated Frapi_ActionTest->testGetParams()
        $this->markTestIncomplete(
        "getParams test not implemented");
        
        $this->action->getParams(/* parameters */);
    
    }

    /**
     * Tests Frapi_Action->getFiles()
     */
    public function testGetFiles ()
    {
        // TODO Auto-generated Frapi_ActionTest->testGetFiles()
        $this->markTestIncomplete(
        "getFiles test not implemented");
        
        $this->action->getFiles(/* parameters */);
    
    }

}

