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
class Default_Form_Error extends Lupin_Form
{
    public function init()
    {
        $tr = Zend_Registry::get('tr');

        $http_code = new Zend_Form_Element_Text('http_code');
        $http_code->setLabel($tr->_('HTTP_CODE'));
        $http_code->setRequired(false);
        $this->addElement($http_code);

        $http_phrase = new Zend_Form_Element_Text('http_phrase');
        $http_phrase->setLabel($tr->_('HTTP_REASON_PHRASE'));
        $http_phrase->setRequired(false);
        $this->addElement($http_phrase);

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel($tr->_('NAME'));
        $name->setRequired(true);
        $name->addValidator('NotEmpty', true, array('messages' => $tr->_('GENERAL_MISSING_TEXT_VALUE')));
        $this->addElement($name);

        $msg = new Zend_Form_Element_Textarea('message');
        $msg->setLabel($tr->_('ERROR_MESSAGE'));
        $msg->setAttribs(array('rows' => 10, 'cols' => 35));
        $msg->setRequired(true);
        $msg->addValidator('NotEmpty', true, array('messages' => $tr->_('GENERAL_MISSING_TEXT_VALUE')));
        $this->addElement($msg);

        /** Maybe next version will have it back.
        $action = new Default_Model_Action;
        $all    = $action->getList();
        $actions = new Zend_Form_Element_Multiselect('actions');
        $actions->setLabel('Associated Actions with this Error');
        $actions->setMultiOptions($all);
        $actions->setAttrib('size', 8);
        $this->addElement($actions);
        */

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
