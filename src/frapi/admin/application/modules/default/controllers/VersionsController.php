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
class VersionsController extends Lupin_Controller_Base
{
    public function init($styles = array())
    {
        $actions = array('index', 'add', 'edit', 'delete');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init($styles);
    }

    public function indexAction()
    {
        $model = new Default_Model_Versions;
        $this->view->data = $model->getAll();
    }

    public function addAction()
    {
        $form  = new Default_Form_Versions;
        $model = new Default_Model_Versions;

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();

                // Save data
                $model->add($data);

                // Bit of xss here and there.
                $this->addMessage('Version ' . $data['name'] . ' added.');
                $this->_redirect('/versions');
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $model = new Default_Model_Versions;
        $form  = new Default_Form_Versions;

        $request = $this->getRequest();
        $id      = $request->getParam('id');
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();

                // Save data
                $model->update($data, $id);
                $this->addMessage('Version updated.');
                $this->_redirect('/versions/edit/id/' . $id);
            }
        } else {
            $version = $model->getVersion($id);
            $form->populate($version);
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

        $model = new Default_Model_Versions;
        $model->delete($id);
        $this->addMessage('Version deleted');
        $this->_redirect('/versions');
    }
}
