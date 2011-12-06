<?php

class MimetypesController extends Lupin_Controller_Base {
    public function init($styles = array())
    {
        $this->tr = Zend_Registry::get('tr');
        $actions = array('index', 'add', 'edit', 'delete', 'sync', 'test', 'code', 'error');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init($styles);
    }
    
    /**
     * Empty error action
     *
     * This is only the action for errors
     *
     * @return void
     */
    public function errorAction() {}

    /**
     * The index
     *
     * This is the index action where we check if the localConfigPath
     * is writeable by the user. If it isn't then we set a warning message.
     *
     * @uses   Default_Model_Action
     * @return void
     */
    public function indexAction()
    {
        $model = new Default_Model_Mimetype;
        $dir = Zend_Registry::get('localConfigPath');
        $dir = $dir . 'mimetypes.xml';

        $dir = Zend_Registry::get('localConfigPath');

        if (!is_writable($dir)) {
            /**
             * @todo Localize
             */
            $actionPathMessage = sprintf($this->tr->_('MIMETYPE_DIR_PROBLEM'), $dir);
            $setupHelpMessage  = $this->tr->_('SETUP_HELP_MESSAGE');

            $this->addMessage(
                $actionPathMessage .' <br /><br />' . $setupHelpMessage
            );

            $this->_redirect('/mimetypes/error');
        }

        $data = $model->getAll();

        if ($data == false) {
            $data = array();
        }

        $this->view->data = $data;
    }
    
    public function addAction()
    {
        $form  = new Default_Form_Mimetype;
        $model = new Default_Model_Mimetype;

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                // Save data
                $model->add($form->getValues());
                $model->refreshAPCCache();
                /**
                 * @todo Localize
                 */
                $this->addMessage(sprintf($this->tr->_('MIMETYPE_ADD_SUCCESS'), $request->getParam('mimetype')));
                $this->_redirect('/mimetypes');
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        /**
         * @todo Localize
         */
        
        $request = $this->getRequest();
        $id      = $request->getParam('id');
        if ($id === null) {
            $this->addErrorMessage($this->tr->_('MIMETYPE_MISSING_ID'));
            return;
        }

        $form  = new Default_Form_Mimetype;
        $model = new Default_Model_Mimetype;
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                // Save data
                $model->update($form->getValues(), $id);
                $model->refreshAPCCache();
                $this->addMessage(sprintf($this->tr->_('MIMETYPE_UPDATE_SUCCESS'), $request->getParam('mimetype')));
                $this->_redirect('/mimetypes/edit/id/' . $id);
            }
        } else {

            $data = $model->get($id);

            $form->populate($data);
        }

        $this->view->form = $form;
    }

    public function deleteAction()
    {
        /**
         * @todo Localize
         */
        
        $id = $this->getRequest()->getParam('id');
        
        if ($id === null) {
            $this->addErrorMessage($this->tr->_('MIMETYPE_MISSING_ID'));
            return;
        }

        $model = new Default_Model_Mimetype;
        $model->delete($id);
        $this->addMessage($this->tr->_('MIMETYPE_DELETE'));
        $this->_redirect('/mimetypes');
    }
}
