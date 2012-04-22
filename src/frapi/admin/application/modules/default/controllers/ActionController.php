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
class ActionController extends Lupin_Controller_Base
{

     private $tr;
    /**
     * Main Initializer
     *
     * This is the public method that will be used by the controller base
     * using the tyles in the init.
     *
     * @param array $styles an array of stylesheets (CSS)
     */
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
        $model = new Default_Model_Action;
        $dir = Zend_Registry::get('localConfigPath');
        $dir = $dir . 'actions.xml';

        $dir = Zend_Registry::get('localConfigPath');

        if (!is_writable($dir)) {

            $actionPathMessage = sprintf($this->tr->_('ACTION_DIR_PROBLEM'), $dir);
            $setupHelpMessage  = $this->tr->_('SETUP_HELP_MESSAGE');

            $this->addMessage(
                $actionPathMessage .' <br /><br />' . $setupHelpMessage
            );

            $this->_redirect('/action/error');
        }

        $data = $model->getAll();

        if ($data == false) {
            $data = array();
        }

        $this->view->data = $data;
    }

    /**
     * Add an action
     *
     * This is the add action method. It literally does what it say.
     * It adds an action.
     *
     * This method has a different output whether or not some data is posted
     * to it.
     *
     * @uses Default_Form_Action
     * @uses Default_Model_Action
     *
     * @return void
     */
    public function addAction()
    {
        $form  = new Default_Form_Action;
        $model = new Default_Model_Action;

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                // Save data
                try {
                    $model->save($request->getParams());
                    $this->addMessage($this->tr->_('ACTION_ADD_SUCCESS') . ': ' . $request->getParam('name'));
                    $this->_redirect('/action');
                } catch (RuntimeException $e) {
                    $this->addErrorMessage($this->tr->_('ACTION_ADD_FAIL') . ': '  .  $request->getParam('name') .
                        '. ' . $e->getMessage());
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * This is the code action
     *
     * This method is not in use right now but was mostly used
     * for the code editor (In browser FRAPI code editor)
     */
    public function codeAction()
    {
        $request = $this->getRequest();
        $this->view->id = $id = $request->getParam('id');
        if ($id === null) {
            $this->addErrorMessage($this->tr->_('ACTION_MISSING_ID'));
            return;
        }

        $model = new Default_Model_Action;

        $data       = $request->getParams();
        $actionData = $model->get($id);
        $name       = $actionData['name'];
        $file       = strtolower($name) . '.php';

        $dir = CUSTOM_PATH . DIRECTORY_SEPARATOR . 'Action';
        $file = $dir . DIRECTORY_SEPARATOR . ucfirst($file);

        if ($this->_request->isPost()) {
            $content = $this->_request->getParam('code');
            if (!is_writable($file)) {
                $this->addMessage($this->tr->_('FILE_NOT_WRITABLE'));
                $this->_redirect('/action/code/id/' . htmlentities($id));
            }

            file_put_contents($file, $content);
            $this->addMessage($this->tr->_('FILE_CONTENT_UPDATED'));
            $this->_redirect('/action');

        } else {
            if (!file_exists($file)) {
                $this->addMessage($this->tr->_('FILE_NOT_EXIST_HAVE_YOU_SYNCED'));
                $this->_redirect('/action');
            }

            $content = file_get_contents($file);
            $this->view->name = $name;
            $this->view->content = $content;
        }
    }

    /**
     * Edit an action
     *
     * This method is invoked whenever an action has to be edited from the
     * administration panel.
     *
     * An action is edited by it's id/hash
     *
     * @return Zend_View
     */
    public function editAction()
    {
        $request = $this->getRequest();
        $this->view->id = $id = $request->getParam('id');
        if ($id === null) {
            $this->addErrorMessage($this->tr->_('ACTION_MISSING_ID'));
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
                try {
                    $model->update($request->getParams(), $id) ;
                    $this->addMessage($this->tr->_('ACTION_UPDATE_SUCCESS') . ': '  . $request->getParam('name'));
                    $this->_redirect('/action/edit/id/' . $id);
                } catch (RuntimeException $e) {
                    $this->addErrorMessage($this->tr->_('ACTION_UPDATE_FAIL') . ': ' . $request->getParam('name') .
                        '. ' . $e->getMessage());
                }
            }
        } else {
            $actionData = $model->get($id);

            $form->populate($actionData);
        }

        $this->view->form = $form;
    }

    /**
     * Delete an action
     *
     * This method is used to delete an action from the list of actions.
     * Once the action is deleted, the user is sent back to the /actions page.
     *
     * @return Zend_View
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id === null) {
            $this->addErrorMessage($this->tr->_('ACTION_MISSING_ID'));
            return;
        }

        $model = new Default_Model_Action;
        $model->delete($id);
        $this->addMessage($this->tr->_('ACTION_DELETE'));
        $this->_redirect('/action');
    }

    /**
     * Synchronize the codebase
     *
     * This method is used to synchronize the codebase and generate the
     * code for the actions that don't exist yet. The synchronisation is
     * done by comparing the existing file names. The ones that don't exist
     * will be created.
     *
     * @return Zend_View
     */
    public function syncAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $model = new Default_Model_Action;

        $dir  = CUSTOM_PATH . DIRECTORY_SEPARATOR . 'Action';
        if (!is_writable($dir)) {
            $this->addMessage(sprintf($this->tr->_('ACTION_WRITE_ERROR'), $dir));
            $this->_redirect('/action');
        }
        $model->sync();
        $this->addMessage($this->tr->_('ACTION_DEV_SYNC_SUCCESS'));
        $this->_redirect('/action');
    }

    public function testAction()
    {
        $name = 'Testing1';
        $dir  = CUSTOM_PATH . DIRECTORY_SEPARATOR . 'Action';
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

    /**
     * Prototype getErrors
     *
     * This method is not used. It is currently a prototype, a holder
     * for reverse engineering the actions and finding the errors
     * in each of the actions and creating them in the database associating
     * them with actions.
     *
     * @param  Iterator $it  The iterator to fetch the parameters from
     * @param  string   $methodName The original method name to parse.
     * @return mixed    Either an array of error or false.
     */
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
