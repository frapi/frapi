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
class ActionController extends Lupin_Controller_Base
{
    public function init()
    {
        $actions = array('index', 'add', 'edit', 'delete', 'sync', 'test', 'code');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init();
    }

    public function indexAction()
    {
        $model = new Default_Model_Action;
        $data = $model->getAll();

        if ($data == false) {
            $data = array();
        }

        $this->view->data = $data;
    }

    public function addAction()
    {
        $form  = new Default_Form_Action;
        $model = new Default_Model_Action;

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                // Save data
                $model->save($request->getParams());
                $this->addMessage('Action ' . $request->getParam('name') . ' added.');
                $this->_redirect('/action');
            }
        }

        $this->view->form = $form;
    }
    
    public function codeAction()
    {
        $request = $this->getRequest();
        $this->view->id = $id = $request->getParam('id');
        if ($id === null) {
            $this->addErrorMessage('ID parameter is missing.');
            return;
        }
        
        $data = $request->getParams();
        
        $model = new Default_Model_Action;

        $actionData = $model->get($id);
        $name = $actionData['name'];
        $file = $name . '.php';
        
        $dir = ROOT_PATH . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'Action';
        $file = $dir . DIRECTORY_SEPARATOR . $file;
        $content = file_get_contents($file);
        $content = str_replace('<?php', '', $content);
        $this->view->name = $name;
        $this->view->content = $content;

    }

    public function editAction()
    {
        $request = $this->getRequest();
        $this->view->id = $id = $request->getParam('id');
        if ($id === null) {
            $this->addErrorMessage('ID parameter is missing.');
            return;
        }

        $data = $request->getParams();

        $form  = new Default_Form_Action;
        $model = new Default_Model_Action;

        $actionData = $model->get($id);
        $params     = array();
        
        if (isset($actionData['parameters']) && !empty($actionData['parameters'])) {
            $params = $actionData['parameters'];
        }

        if (isset($params['parameter']) && !isset($params['parameter'][0])) {
            $params['parameter'] = array($params['parameter']);
        }

        $this->view->data = $params;

        $model = new Default_Model_Action;
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                // Save data
                // This is xss right there.
                $model->update($request->getParams(), $id);
                $this->addMessage('Action ' . $request->getParam('name') . ' updated.');
                $this->_redirect('/action/edit/id/' . $id);
            }
        } else {
            $actionData = $model->get($id);

            $actionData['use_custom_route'] = isset($actionData['route']) ? 1: 0;
            $form->populate($actionData);
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

        $model = new Default_Model_Action;
        $model->delete($id);
        $this->addMessage('Action deleted');
        $this->_redirect('/action');
    }

    public function syncAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $model = new Default_Model_Action;
        
        $dir  = ROOT_PATH . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'Action';
        if (!is_writable($dir)) {
            $this->addMessage(
                'The path : "' . $dir . '" is not currently writeable by this user, ' . 
                'therefore we cannot synchronize the codebase'
            );
            $this->_redirect('/action');
        }
        $model->sync();
        $this->addMessage('Development environment has been sychronized');
        $this->_redirect('/action');
    }
    
    public function testAction()
    {
        $name = 'Testing1';
        $dir  = ROOT_PATH . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'Action';
        $file = $dir . DIRECTORY_SEPARATOR . $name . '.php';
        require_once $file;
        
        $class = Zend_CodeGenerator_Php_Class::fromReflection(
            new Zend_Reflection_Class('Action_' . $name)
        );

        $error  = array();
        $errors = array();
        
        foreach ($class->getMethods() as $key => $method) {
            $body = $method->getBody();
            $methodName = $method->getName();

            $toks = token_get_all('<?' . 'php ' . $body . '?>');
            
            $it = new ArrayIterator($toks);
            
            for ($it->rewind(); $it->valid(); $it->next()) {
                $current = $it->current();
                $key = $it->key();
                
                $item = (double)$current[0];

                if ($item == T_STRING && stristr('frapi_error', $current[1]) !== false) {
                    if ($error = $this->getErrors($it, $methodName)) {
                        $errors[] = $error;
                    }
                }
                
            }
        }
        
        //print_r($errors);

        die();
        
    }
    
    private function getErrors($it, $methodName)
    {
        $error  = array();
        $i = 0;
        while ($it->valid()) {
            $it->next();
            $mainKey = $key = $it->key();
            $current = $it->current();

            if ((double)$current[0] == T_CONSTANT_ENCAPSED_STRING) {

                $error[$methodName][$key][$i] = array();
                $error[$methodName][$key][$i]['name'] = $current[1];

                while ($it->valid()) {
                    $it->next();
                    $current = $it->current();
                    
                    if ((double)$current[0] == T_CONSTANT_ENCAPSED_STRING) {
                        $error[$methodName][$key]['message'] = $current[1];

                        while ($it->valid()) {
                            
                            $it->next();
                            $current = $it->current();
                            if ((double)$current[0] == T_LNUMBER) {
                                $error[$methodName][$key]['code'] = $current[1];
                                
                            }
                        }
                    } else {
                        if ($it->offsetExists($mainKey)) {
                            $it->seek($mainKey);
                        }
                    }
                }

                ++$i;
            } else {

            }
        }
        
        return !empty($error) ? $error : false;
    }
}