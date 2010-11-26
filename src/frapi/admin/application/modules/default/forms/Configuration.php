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
class Default_Form_Configuration extends Lupin_Form
{
    public function init()
    {
        $tr = Zend_Registry::get('tr');
        
        $api_url = new Zend_Form_Element_Text('api_url');
        $api_url->setLabel($tr->_('API_DOMAIN') . ' ' . $tr->_('FOR_TESTER'));
        $api_url->setRequired(true);
        $api_url->addValidator('NotEmpty', true, array('messages' => $tr->_('GENERAL_MISSING_TEXT_VALUE')));
        $this->addElement($api_url);
        
        $this->addElement(new Zend_Form_Element_Submit($tr->_('UPDATE_CONFIGURATION'), array('label' => $tr->_('UPDATE_CONFIGURATION'))));
        parent::init();
    }
}
