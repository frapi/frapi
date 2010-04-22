<?php
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
        $params     = $actionData['parameters'];

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

        
        $error = array();
        
        foreach ($class->getMethods() as $key => $method) {
            $body = $method->getBody();
            $methodName = $method->getName();
            $regex = '@frapi_error\(.*?\)@i';
            preg_match_all($regex, $body, $matches, PREG_PATTERN_ORDER);
            print_r($matches);
            continue;
            $toks = token_get_all('<?' . 'php ' . $body . '?>');
            $it = new ArrayIterator($toks);
            for ($it->rewind(); $it->valid(); $it->next()) {
                
                $current = $it->current();
                $key = $it->key();
                $item = (double)$current[0];
                
                if ($item == T_STRING && stristr('frapi_error', $current[1]) !== false) {
                    $error[$methodName][$key] = array();
                    while ($it->valid()) {
                        $it->next();
                        $current = $it->current();
                        
                        if ((double)$current[0] == T_CONSTANT_ENCAPSED_STRING) {
                            $error[$methodName][$key]['name'] = $current[1];
                            
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
                                }
                            }
                        }
                    }
                }
            }
        }
        
        print_r($error);

        die();
        
    }
}