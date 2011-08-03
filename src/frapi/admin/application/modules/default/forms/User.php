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
class Default_Form_User extends Lupin_Form
{
    public function init()
    {
        $tr = Zend_Registry::get('tr');
        
        $handle = new Zend_Form_Element_Text('handle');
        $handle->setLabel($tr->_('HANDLE'));
        $handle->setRequired(true);
        $handle->addValidator('NotEmpty', true, array('messages' => $tr->_('GENERAL_MISSING_TEXT_VALUE')));
        $this->addElement($handle);

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel($tr->_('PASSWORD'));
        $password->setRequired(true);
        $password->addValidator('NotEmpty', true, array('messages' => $tr->_('GENERAL_MISSING_TEXT_VALUE')));
        $this->addElement($password);

        $password1 = new Zend_Form_Element_Password('password_again');
        $password1->setLabel($tr->_('RETYPE') . ' ' . $tr->_('PASSWORD'));
        $password1->setRequired(true);
        $password1->addValidator('NotEmpty', true, array('messages' => $tr->_('GENERAL_MISSING_TEXT_VALUE')));
        $this->addElement($password1);

        $this->addElement(new Zend_Form_Element_Submit($tr->_('SUBMIT')));

        parent::init();
    }
}
