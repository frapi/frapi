<?php
class IndexController extends Lupin_Controller_Base
{
    public function init()
    {
        $this->_helper->_acl->allow('admin', array('index'));
        parent::init();
    }

    public function indexAction()
    {
    }
}
