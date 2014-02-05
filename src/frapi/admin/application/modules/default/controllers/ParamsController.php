<?php
/**
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
 * @license   New BSD
 * @package   frapi-admin
 */
class ParamsController extends Lupin_Controller_Base
{
  public function init($styles = array())
    {
        $this->tr = Zend_Registry::get('tr');
        $actions = array('index', 'add', 'edit', 'delete');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init($styles);
    }

    public function indexAction()
    {
        $model = new Default_Model_Param;
        $data = $model->getAll();
        if ($data == false) {
            $data = array();
        }
        
        $this->view->data = $data;
    }

    public function addAction()
    {
        $form = new Default_Form_Param;
        $model   = new Default_Model_Param;
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                // Save data
                $model->add($form->getValues());
                $this->addMessage(sprintf($this->tr->_('PARAM_ADD_SUCCESS'),$request->getParam('key')));
                $this->_redirect('/params');
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $id      = $request->getParam('id');
        if ($id === null) {
            $this->addErrorMessage($this->tr->_('ACTION_MISSING_ID'));
            return;
        }

        $form = new Default_Form_Param;

        $model = new Default_Model_Param;
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $data = $request->getPost();
                $model->edit($data, $id);
                
                $this->addMessage(sprintf($this->tr->_('PARAM_UPDATE_SUCCESS'), $request->getParam('key')));
                $this->_redirect('/params');
            }
        } else {
            $form->populate($model->get($id));
        }

        $this->view->form = $form;
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id === null) {
            $this->addErrorMessage($this->tr->_('ACTION_MISSING_ID'));
            return;
        }

        $model = new Default_Model_Param;
        $model->delete($id);
        $this->addMessage($this->tr->_('PARAM_DELETE'));
        $this->_redirect('/params');
    }
}
