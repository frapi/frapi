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
class PartnersController extends Lupin_Controller_Base
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
        $model   = new Default_Model_Partner;
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                // Save data
                $model->add($form->getValues());
                $this->addMessage(sprintf($this->tr->_('PARTNER_ADD_SUCCESS'),$request->getParam('company')));
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
            $this->addErrorMessage($this->tr->_('ACTION_MISSING_ID'));
            return;
        }

        $form = new Default_Form_Partner;

        $apiKey = new Zend_Form_Element_Text('api_key');
        $apiKey->setLabel('API Key');
        $apiKey->setAttrib('disabled', true);
        $form->addElement($apiKey);

        $generate = new Zend_Form_Element_Submit('generate_api_key');
        $generate->setLabel($this->tr->_('PARTNER_GENERATE_NEW_API_KEY'));
        $form->addElement($generate);

        $model = new Default_Model_Partner;
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $data = $request->getPost();
                $model->edit($data, $id);
                
                if (isset($data['generate_api_key'])) {
                    $model->updateAPIKey($id);
                    $this->addMessage($this->tr->_('PARTNER_API_KEY_UPDATED'));
                }
                $this->addMessage(sprintf($this->tr->_('PARTNER_UPDATE_SUCCESS'), $request->getParam('company')));
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
            $this->addErrorMessage($this->tr->_('ACTION_MISSING_ID'));
            return;
        }

        $model = new Default_Model_Partner;
        $model->delete($id);
        $this->addMessage($this->tr->_('PARTNER_DELETE'));
        $this->_redirect('/partners');
    }
}
