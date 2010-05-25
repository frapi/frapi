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
class Default_Form_Error extends Lupin_Form
{
    public function init()
    {
        $http_code = new Zend_Form_Element_Text('http_code');
        $http_code->setLabel('HTTP Code');
        $http_code->setRequired(false);
        $this->addElement($http_code);
        
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name');
        $name->setRequired(true);
        $this->addElement($name);

        $msg = new Zend_Form_Element_Textarea('message');
        $msg->setLabel('Error Message');
        $msg->setAttribs(array('rows' => 10, 'cols' => 40));
        $msg->setRequired(true);
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
        $desc->cols = 40;
        $desc->rows = 15;
        $desc->setLabel('Description');
        $desc->setRequired(false);
        $this->addElement($desc);

        $this->addElement(new Zend_Form_Element_Submit('submit'));

        parent::init();
    }
}