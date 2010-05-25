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
class Default_Form_User extends Lupin_Form
{
    public function init()
    {
        $handle = new Zend_Form_Element_Text('handle');
        $handle->setLabel('Handle');
        $handle->setRequired(true);
        $this->addElement($handle);

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password');
        $password->setRequired(true);
        $this->addElement($password);

        $password1 = new Zend_Form_Element_Password('password_again');
        $password1->setLabel('Re-type Password');
        $password1->setRequired(true);
        $this->addElement($password1);

        $this->addElement(new Zend_Form_Element_Submit('submit'));

        parent::init();
    }
}
