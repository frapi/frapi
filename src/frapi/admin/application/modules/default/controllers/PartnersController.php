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
class PartnersController extends Lupin_Controller_Base
{
    public function init()
    {
        $actions = array('index', 'add', 'edit', 'delete');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init();
    }

    public function indexAction()
    {
        $model = new Default_Model_Partner;
        $data = $model->getAll();
        if ($data == false) {
            $data = array();
        }
        
        $this->view->data = $data;
    }

    public function addAction()
    {
        $form = new Default_Form_Partner;
        $form->addElement(new Zend_Form_Element_Submit('submit'));

        $model   = new Default_Model_Partner;
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                // Save data
                $model->add($form->getValues());
                $this->addMessage(
                    'The partner has been added. Remember that if you want to make an API call ' . 
                    'require a partner authentication, you have to uncheck "Is the action public?" in the ' .
                    'action edition/add section. Psstt the RESTful API uses HTTP Auth.'
                );
                $this->_redirect('/partners');
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

        $form = new Default_Form_Partner;

        $apiKey = new Zend_Form_Element_Text('api_key');
        $apiKey->setLabel('API Key');
        $apiKey->setAttrib('disabled', true);
        $form->addElement($apiKey);

        $form->addElement(new Zend_Form_Element_Submit('submit'));

        $generate = new Zend_Form_Element_Submit('generate_api_key');
        $generate->setLabel('Generate a New API Key');
        $form->addElement($generate);

        $model = new Default_Model_Partner;
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $data = $request->getPost();
                $model->edit($data, $id);
                
                if (isset($data['generate_api_key'])) {
                    $model->updateAPIKey($id);
                }

                $this->addMessage('Partner ' . $request->getParam('company') . ' updated.');
                $this->_redirect('/partners/edit/id/' . $id);
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
            $this->addErrorMessage('ID parameter is missing.');
            return;
        }

        $model = new Default_Model_Partner;
        $model->delete($id);
        $this->addMessage('Partner deleted');
        $this->_redirect('/partners');
    }
}
