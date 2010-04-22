<?php

class Default_Form_Configuration extends Lupin_Form
{
    public function init()
    {
        $api_url = new Zend_Form_Element_Text('api_url');
        $api_url->setLabel('API Domain (for Tester)');
        $api_url->setRequired(true);
        $this->addElement($api_url);
        
        $this->addElement(new Zend_Form_Element_Submit('Update Configuration'));
        
        parent::init();
    }
}