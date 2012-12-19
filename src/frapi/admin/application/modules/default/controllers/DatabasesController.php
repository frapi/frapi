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
class DatabasesController extends Lupin_Controller_Base
{
    private $tr;

    public function init($styles = array())
    {
        $this->tr = Zend_Registry::get('tr');    
        $actions = array('index', 'add', 'edit','delete');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init($styles);
    }

    public function indexAction()
    {
        $model = new Default_Model_Database();
        $data = $model->getAll();
        if ($data == false) {
            $data = array();
        }
        
        $this->view->data = $data;
    }

    public function addAction()
    {
        $form = new Default_Form_Database();
        $this->view->form = $form;

        $data = $this->_request->getParams();

        if ($this->_request->isPost()) {
            if ($form->isValid($data)) {
                $model = new Default_Model_Database();
                $res = $model->add($data);
                if ($res !== false) {
                    $this->addMessage('Database added with great success!');
                    $this->_redirect('/databases');
                }
            }
        }
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $id      = $request->getParam('id');
        if ($id === null) {
            $this->addErrorMessage($this->tr->_('ACTION_MISSING_ID'));
            return;
        }

        $form = new Default_Form_Database;
          
        $model = new Default_Model_Database;
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $data = $request->getPost();
                $model->edit($data, $id);
                
                $this->addMessage(sprintf($this->tr->_('DATABASE_UPDATE_SUCCESS'), $request->getParam('dbname')));
                $this->_redirect('/databases');
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
          $model = new Default_Model_Database;
          $model->delete($id);
          $this->addMessage($this->tr->_('DATABASE_DELETE'));
          $this->_redirect('/databases');
     }
      
}
