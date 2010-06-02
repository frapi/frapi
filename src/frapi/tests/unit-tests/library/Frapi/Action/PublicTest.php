<?php

/**
 * Public Action test case
 * 
 * Key 'public_test' is a required parameter
 */
class Frapi_Action_PublicTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Frapi_Action
     * 
     * Action under test
     */
    private $_action;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
        $_SERVER['HTTP_HOST'] = 'testing';
        $this->_action        = MockFrapi_Action::getInstance('public');
        $params               = array('public_test' => 'publicActionTest');
        
        $this->_action->setActionParams($params);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->_action = null;
        parent::tearDown();
    }
    
    public function testExecuteGetWithoutRequiredParam() {
        $this->setExpectedException('Frapi_Action_Exception', 'Missing required parameters');
        $this->_action->setActionParams(array());
        $get = $this->_action->executeGet();
    }
    
    public function testExecuteGet() {
        $get = $this->_action->executeGet();
        $this->assertEquals(2, count($get));
        $this->assertArrayHasKey('public_test', $get);
        $this->assertEquals('get', $get['method']);
    }
    
    public function testExecutePost() {
        $post = $this->_action->executePost();
        $this->assertArrayHasKey('success', $post);
        $this->assertEquals('post', $post['method']);
        $this->assertEquals(3, count($post));
    }
    
    public function testExecutePut() {
        $put = $this->_action->executePut();
        $this->assertArrayHasKey('success', $put);
        $this->assertEquals('put', $put['method']);
        $this->assertEquals(3, count($put));
    }
    
    public function testExecuteDeleteDoNotPassDeleteKey() {
        $this->setExpectedException('Frapi_Error', 'Required delete key is missing');
        $delete = $this->_action->executeDelete();
    }
    
    public function testExecuteDeletePassWillNotDelete() {
        $this->setExpectedException('Frapi_Error', 'No record matches the provided key');
        $this->_action->setActionParams(array('delete' => 10, 'public_test' => 'w00t!'));
        $delete = $this->_action->executeDelete();
    }
    
    public function testExecuteDeleteSuccessful() {
        $this->_action->setActionParams(array('delete' => 12, 'public_test' => 'w00t!'));
        $delete = $this->_action->executeDelete();
        $this->assertEquals(1, $delete['success']);
    }
    
    public function testExecuteHead() {
        $head = $this->_action->executeHead();
        $this->assertEquals('meta-data', $head['head']);
        $this->assertEquals(2, count($head));
    }
}

