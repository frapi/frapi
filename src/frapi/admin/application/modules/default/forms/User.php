<?php

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
