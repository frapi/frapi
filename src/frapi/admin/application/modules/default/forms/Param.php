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
class Default_Form_Param extends Lupin_Form
{
    public function init()
    {
        $tr = Zend_Registry::get('tr');

        $key = new Zend_Form_Element_Text('key');
        $key->setLabel($tr->_('KEY'));
        $key->setAttrib('size','50');
        $this->addElement($key);

        $value = new Zend_Form_Element_Text('value');
        $value->setLabel($tr->_('VALUE'));
        $value->setAttrib('size','50');
        $this->addElement($value);

        $this->addElement(new Zend_Form_Element_Submit($tr->_('SUBMIT')));

        parent::init();
    }


}
