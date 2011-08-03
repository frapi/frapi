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
class Default_Form_Language extends Lupin_Form
{
    public function init()
    {
        $tr = Zend_Registry::get('tr');
        $locale = Zend_Registry::get('Zend_Locale');

        $available_languages = array (
            'en' => 'English',
            'fr' => 'Francais',
            'it' => 'Italiano',
            'ru' => 'Русский',
        );

        $this->setAction('/language');
        $this->setAttrib('id', 'language-form');

        $languages = new Zend_Form_Element_Select('languages');
        $languages->setLabel($tr->_('LANGUAGE'));
        $languages->addMultiOptions($available_languages);
        $languages->setValue(isset($locale->value) ? $locale->value : 'en');
        $this->addElement($languages);

        $system_wide = new Zend_Form_Element_Checkbox('system_wide');
        $system_wide->setLabel($tr->_('UPDATE_SYSTEM_WIDE') . '?');
        $system_wide->setRequired(false);
        $this->addElement($system_wide);

        $this->addElement(new Zend_Form_Element_Submit($tr->_('SAVE')));

        parent::init();
    }
}
