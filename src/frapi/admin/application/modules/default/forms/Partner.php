<?php

class Default_Form_Partner extends Lupin_Form
{
    public function init()
    {
        $name = new Zend_Form_Element_Text('firstname');
        $name->setLabel('First Name');
        $name->setRequired(true);
        $this->addElement($name);

        $lastname = new Zend_Form_Element_Text('lastname');
        $lastname->setLabel('Last Name');
        $lastname->setRequired(true);
        $this->addElement($lastname);

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email/Username');
        $email->setRequired(true);
        $this->addElement($email);

        $company = new Zend_Form_Element_Text('company');
        $company->setLabel('Company');
        $company->setRequired(true);
        $this->addElement($company);
    }
}
