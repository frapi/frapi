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
class OutputController extends Lupin_Controller_Base
{
    public function init($styles = array())
    {
        $actions = array('index', 'sync', 'makedefault', 'disable', 'enable');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init($styles);
    }

    public function indexAction()
    {
        $model = new Default_Model_Output;
        $this->view->data = $model->getAll();
    }

    public function syncAction()
    {
        $model = new Default_Model_Output;
        $model->sync();
        $this->_redirect('/output');
    }

    public function makedefaultAction()
    {
        $model = new Default_Model_Output;
        $model->makeDefault($this->getRequest()->getParam('id'));

        $this->refreshAPCCache();
        $this->_redirect('/output');
    }

    public function enableAction()
    {
        $model = new Default_Model_Output;
        $model->enable($this->getRequest()->getParam('id'));
        $this->_redirect('/output');
    }

    public function disableAction()
    {
        $model = new Default_Model_Output;
        $model->disable($this->getRequest()->getParam('id'));
        $this->_redirect('/output');
    }
    
    /**
     * Refresh the APC cache by deleting APC entries.
     *
     * @return void
     */
    public function refreshAPCCache()
    {
        $configModel = new Default_Model_Configuration();
        $server = $configModel->getKey('api_url');
        $hash = isset($server) ? hash('sha1', $server) : '';
        
        $cache = Frapi_Cache::getInstance(FRAPI_CACHE_ADAPTER);

        $cache->delete($hash . '-Output.default-format');
    }
}