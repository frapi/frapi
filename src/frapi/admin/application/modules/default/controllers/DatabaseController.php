<?php
class DatabaseController extends Lupin_Controller_Base
{
    public function init()
    {
        $actions = array('index', 'add', 'edit');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init();
    }

    public function indexAction() 
    {
        $model = new Default_Model_Configuration();
        $this->view->configs = $model->getDbConfig();
    }
    
    public function addAction()
    {
        $form = new Default_Form_Database();
        $this->view->form = $form;
        
        $data = $this->_request->getParams();
        
        if ($this->_request->isPost()) {
            if ($form->isValid($data)) {
                $model = new Default_Model_Configuration();
                $res = $model->addDb($data);
                if ($res !== false) {
                    $this->addMessage('Database added with great success!');
                    $this->_redirect('/database');
                }
            }
        }
    }
    
    public function editAction()
    {
        $form = new Default_Form_Database();
        $model = new Default_Model_Configuration();
        
        $this->view->form = $form;
        
        $data = $this->_request->getParams();
        
        if ($this->_request->isPost()) {
            if ($form->isValid($data)) {
                $res = $model->editDb($data);
                if ($res !== false) {
                    $this->addMessage('Database updated with great success!');
                    $this->_redirect('/database');
                }
            }
        } else {
            $res = $model->getDbConfig();
            $keys = array();
            foreach ($res as $key => $value) {
                $keys[$value['key']] = $value['value'];
            }
            $form->populate($keys);
            $this->view->form = $form;
        }
    }
}