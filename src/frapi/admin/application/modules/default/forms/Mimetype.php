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
class Default_Form_Mimetype extends Lupin_Form
{
    public function init()
    {
        /**
         * @todo Localize
         */
        
        $tr = Zend_Registry::get('tr');

        $name = new Zend_Form_Element_Text('mimetype');
        $name->setLabel($tr->_('MIMETYPE'));
        $name->setRequired(true);
        $name->addValidator('NotEmpty', true, array('messages' => $tr->_('GENERAL_MISSING_TEXT_VALUE')));
        $this->addElement($name);
        
        $name = new Zend_Form_Element_Text('output_format');
        $name->setLabel($tr->_('OUTPUT_FORMAT'));
        $name->setRequired(true);
        $name->addValidator('NotEmpty', true, array('messages' => $tr->_('GENERAL_MISSING_TEXT_VALUE')));
        $this->addElement($name);

        $desc = new Zend_Form_Element_Textarea('description');
        $desc->cols = 35;
        $desc->rows = 15;
        $desc->setLabel($tr->_('DESCRIPTION'));
        $desc->setRequired(false);
        $this->addElement($desc);

        $this->addElement(new Zend_Form_Element_Submit($tr->_('SUBMIT')));

        parent::init();
    }
}
