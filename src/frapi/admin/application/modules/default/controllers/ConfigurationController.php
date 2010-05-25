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
class ConfigurationController extends Lupin_Controller_Base
{
    public function init()
    {
        $actions = array('index');
        $this->_helper->_acl->allow('admin', $actions);
        
        parent::init();
    }

    public function indexAction() 
    {
        $config_model = new Default_Model_Configuration();
        $form         = new Default_Form_Configuration();
        $data         = $this->_request->getParams();
        
        if ($this->_request->isPost()) {
            if ($form->isValid($data)) {
                $res = $config_model->updateApiUrl($data['api_url']);
                
                if ($res !== false) {
                    $this->addMessage('Configuration updated!');
                    $this->_redirect('/configuration');
                }
            }
        } else {
            $form->populate(array(
                'api_url' => $config_model->getKey('api_url')
            ));
            
            $this->view->form = $form;
        }
    }
}