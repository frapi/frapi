<?php

class Default_Form_Database extends Lupin_Form
{
    public function init()
    {
        $hostname = new Zend_Form_Element_Text('db_hostname');
        $hostname->setLabel('Hostname');
        $this->addElement($hostname);

        $username = new Zend_Form_Element_Text('db_username');
        $username->setLabel('Username');
        $this->addElement($username);

        $password = new Zend_Form_Element_Text('db_password');
        $password->setLabel('password');
        $this->addElement($password);
        
        $database = new Zend_Form_Element_Text('db_database');
        $database->setLabel('database');
        $this->addElement($database);
        
        $this->addElement(new Zend_Form_Element_Submit('submit'));
        
        parent::init();
    }
}