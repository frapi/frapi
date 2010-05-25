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
 * @copyright echolibre ltd.
 * @package   frapi-admin
 */
class UserController extends Lupin_Controller_Base
{
    public function init()
    {
        $actions = array('index', 'add', 'edit', 'delete');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init();
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
                    $this->addErrorMessage('Passwords do not match, please try again.');
                    $this->_redirect('/user/add');
                }

                // Save data
                $model->add($data);

                // Bit of xss here and there.
                $this->addMessage('User ' . $data['handle'] . ' added.');
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
                    $this->addErrorMessage('Passwords do not match, please try again.');
                    $this->_redirect('/user/edit/id/' . $data['id']);
                }

                // Save data
                $model->update($data, $id);
                $this->addMessage('User updated.');
                $this->_redirect('/user/edit/id/' . $id);
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
            $this->addErrorMessage('ID parameter is missing.');
            return;
        }

        $model = new Default_Model_User;
        $model->delete($id);
        $this->addMessage('User deleted');
        $this->_redirect('/user');
    }
}