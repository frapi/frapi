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
class ConfigurationController extends Lupin_Controller_Base
{
    private $tr;

    public function init($styles = array())
    {
        $this->tr = Zend_Registry::get('tr');
        $actions = array('index');
        $this->_helper->_acl->allow('admin', $actions);

        parent::init($styles);
    }

    public function indexAction()
    {
        $config_model = new Default_Model_Configuration();
        $form         = new Default_Form_Configuration();
        $lang_form    = new Default_Form_Language();

        $config_dir = Zend_Registry::get('localConfigPath');
        $config_file = $config_dir . 'configurations.xml';

        // Lets make sure our permissions are ok before the user modifies config
        if (!is_writable($config_dir)) {
            $configPathMessage = sprintf($this->tr->_('ACTION_DIR_PROBLEM'), $config_dir);
            $setupHelpMessage  = $this->tr->_('SETUP_HELP_MESSAGE');
            $this->addErrorMessage(
                $configPathMessage .' <br /><br />' . $setupHelpMessage
            );
        } elseif (!is_writable($config_file)) {
            $configFileMessage = sprintf($this->tr->_('FILE_NOT_WRITABLE'), $config_file);
            $setupHelpMessage  = $this->tr->_('SETUP_HELP_MESSAGE');
            $this->addErrorMessage(
                 $configFileMessage .' <br /><br />' . $setupHelpMessage
            );
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                try {
                    $res              = $config_model->updateApiUrl($request->getParam('api_url'));
                    $useCdata         = $request->getParam('cdata');
                    $res2             = $config_model->updateUseCdata($useCdata);
                    $allowCrossDomain = $request->getParam('allow_cross_domain');
                    $res3             = $config_model->updateAllowCrossDomain($allowCrossDomain);

                    if ($res !== false && $res2 !== false && $res3 !== false) {
                        $this->addMessage($this->tr->_('CONFIG_UPDATE_SUCCESS'));
                        $this->_redirect('/configuration');
                    } else  {
                        $this->addErrorMessage($this->tr->_('CONFIG_UPDATE_FAIL'));
                    }
                } catch (RuntimeException $e) {
                    $this->addErrorMessage($this->tr->_('CONFIG_UPDATE_FAIL') . ": " . $e->getMessage());
                }
            }
        } else {
            $form->populate(array(
                'api_url'            => $config_model->getKey('api_url'),
                'cdata'              => $config_model->getKey('use_cdata'),
                'allow_cross_domain' => $config_model->getKey('allow_cross_domain')
            ));
        }
        $this->view->form = $form;
        $this->view->lang_form = $lang_form;
    }
}
