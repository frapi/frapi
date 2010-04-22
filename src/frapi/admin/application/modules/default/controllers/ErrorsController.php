<?php
class ErrorsController extends Lupin_Controller_Base
{
    public function init()
    {
        $actions = array('index', 'add', 'edit', 'delete');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init();
    }

    public function indexAction()
    {
        $model = new Default_Model_Error;
        $data = $model->getAll();
        if ($data == false) {
            $data = array();
        }
        
        $this->view->data = $data;
    }

    public function addAction()
    {
        $form  = new Default_Form_Error;
        $model = new Default_Model_Error;

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                // Save data
                $model->add($form->getValues());
                $model->refreshAPCCache();
                $this->addMessage('Error code ' . $request->getParam('name') . ' added.');
                $this->_redirect('/errors');
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $id      = $request->getParam('id');
        if ($id === null) {
            $this->addErrorMessage('ID parameter is missing.');
            return;
        }

        $form  = new Default_Form_Error;
        $model = new Default_Model_Error;
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                // Save data
                $model->update($form->getValues(), $id);
                
                $model->refreshAPCCache();
                
                $this->addMessage('Error code ' . $request->getParam('name') . ' updated.');
                $this->_redirect('/errors/edit/id/' . $id);
            }
        } else {

            $data = $model->get($id);

            $form->populate($data);
        }

        $this->view->form = $form;
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id === null) {
            $this->addErrorMessage('ID parameter is missing.');
            return;
        }

        $model = new Default_Model_Error;
        $model->delete($id);
        $this->addMessage('Error code deleted');
        $this->_redirect('/errors');
    }
}