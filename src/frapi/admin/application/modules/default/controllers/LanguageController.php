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
class LanguageController extends Lupin_Controller_Base
{
    public function init($styles = array())
    {
        $actions = array('index');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init($styles);
    }

    public function indexAction()
    {
        $config_model = new Default_Model_Configuration();
        $data         = $this->_request->getParams();

        if ($this->_request->isPost()) {

            if (isset($data['system_wide']) && $data['system_wide'] == 1) {
                $config_model->updateLocale($data['languages']);
            }

            $localeSession = new Zend_Session_Namespace('locale');
            $translate = Zend_Registry::get('tr');

            $translate->setLocale($data['languages']);

            $locale = new Zend_Locale($data['languages']);
            Zend_Registry::set('locale', $locale);
            Zend_Registry::set('tr', $translate);

            $localeSession->value = $data['languages'];
            $this->_redirect('/configuration');
        }
    }
}
