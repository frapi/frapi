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
class Default_Form_Tester extends Lupin_Form
{
    public function init()
    {
        $routesAndActions = array('Actions' => array(), 'Routes' => array());
        $model = new Default_Model_Action();
        $allActions = $model->getAll();
        foreach ($allActions as $db_action) {
            $routesAndActions ['Actions'][$db_action['hash']] = $db_action['name'];
            if (!empty($db_action['route'])) {
                $routesAndActions ['Routes']['route-' . $db_action['hash']] = $db_action['route'];
            }
        }
        $action = new Zend_Form_Element_Select('action');
        $action->setLabel('Action');
        $action->addMultiOptions($routesAndActions);
        $this->addElement($action);
        
        $params = new Zend_Form_Element_Text('query_uri');
        $params->setLabel('Query URI');
        $params->setRequired(true);
        $params->setAttrib('size', '80');
        $this->addElement($params);
        
        $t = new Default_Model_Tester;
        $output = $t->buildForm();
        $p = new Lupin_Form_Element_Static('params');
        $p->setValue($output);
        $p->setLabel('Params');
        $this->addElement($p);
        
        $formats = array();
        $outputModel = new Default_Model_Output;
        $format = new Zend_Form_Element_Select('format');
        $format->setLabel('Format');
        $format->setRequired(true);
        foreach ($outputModel->getAll() as $key => $db_format) {
            $formats [$db_format["name"]]= $db_format["name"] . ((!$db_format["enabled"])?(" (disabled)"):(''));
            if ($db_format["default"] != '0') {
                $format->setValue(array($db_format["name"], $db_format["name"]));
            }
        }
        $format->addMultiOptions($formats);
        $this->addElement($format);
        
        $methods = array(
            'get'    => 'GET',
            'post'   => 'POST',
            'put'    => 'PUT',
            'delete' => 'DELETE',
            'head'   => 'HEAD'
        );
        
        $method = new Zend_Form_Element_Select('method');
        $method->setLabel('Method');
        $method->setRequired(true);
        $method->addMultiOptions($methods);
        $this->addElement($method);
        
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email');
        $email->setAttrib('size', '40');
        $this->addElement($email);
        
        $key = new Zend_Form_Element_Text('key');
        $key->setLabel('Secret Key');
        $key->setAttrib('size', '40');
        $this->addElement($key);
        
        $email_key = new Zend_Form_Element_Select('email-key');
        $email_key->setLabel('Load Email/Key Combo');
        $params->setAttrib('size', '55');
        $emails_keys = array(0 => 'Select a combination to fill the fields above.');
        $partnerModel = new Default_Model_Partner();
        $partners = $partnerModel->getAll();

        if (!empty($partners)) {
            foreach ($partners as $key => $partner) {
                if (!empty($partner)) {
                    $emails_keys [$partner['hash']] = $partner['email'] . ' / ' . $partner['api_key'];
                }
            }
        }
        
        $email_key->addMultiOptions($emails_keys);
        $this->addElement($email_key);
        
        $config_model = new Default_Model_Configuration();
        $url = new Zend_Form_Element_Text('url');
        $url->setLabel('API Domain');
        $url->setRequired(true);
        $url->setValue($config_model->getKey("api_url"));
        $this->addElement($url);

        parent::init();
    }
}
