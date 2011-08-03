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
class UserController extends Lupin_Controller_Base
{
    private $tr;

    public function init($styles = array())
    {
        $this->tr = Zend_Registry::get('tr');
        $actions = array('index', 'add', 'edit', 'delete');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init($styles);
    }

    public function indexAction()
    {
        $model = new Default_Model_User;
        $this->view->data = $model->getAll();
    }

    public function addAction()
    {
        $form  = new Default_Form_User;
        $model = new Default_Model_User;

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                if ($data['password'] !== $data['password_again']) {
                    $this->addErrorMessage($this->tr->_('USER_PASSWORD_MISMATCH'));
                    $this->view->form = $form;
                    return;
                }

                // Save data
                $model->add($data);

                // Bit of xss here and there.
                $this->addMessage(sprintf($this->tr->_('USER_ADD_SUCCESS'), $data['handle']));
                $this->_redirect('/user');
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $model = new Default_Model_User;
        $form  = new Default_Form_User;
        $form->removeElement('handle');

        $request = $this->getRequest();
        $id      = $request->getParam('id');
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                if ($data['password'] !== $data['password_again']) {
                    $this->addErrorMessage($this->tr->_('USER_PASSWORD_MISMATCH'));
                    $this->view->form = $form;
                    return;
                }

                // Save data
                $model->update($data, $id);
                $this->addMessage($this->tr->_('USER_UPDATE_SUCCESS'));
                $this->_redirect('/user');
            }
        } else {
            $user = $model->getUser($id);
            $this->view->handle = isset($user['handle']) ? $user['handle'] : false;
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

        $model = new Default_Model_User;
        $model->delete($id);
        $this->addMessage($this->tr->_('USER_DELETE'));
        $this->_redirect('/user');
    }
}
