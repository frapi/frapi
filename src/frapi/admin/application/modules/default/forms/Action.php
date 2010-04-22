<?php

class Default_Form_Action extends Lupin_Form
{
    public function init()
    {
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name');
        $name->setRequired(true);
        $this->addElement($name);

        $enabled = new Zend_Form_Element_Checkbox('enabled');
        $enabled->setLabel('Is the action enabled ?');
        $this->addElement($enabled);

        $public = new Zend_Form_Element_Checkbox('public');
        $public->setLabel('Is the action public ?');
        $this->addElement($public);
        
        $use_custom_route = new Zend_Form_Element_Checkbox('use_custom_route');
        $use_custom_route->setLabel('Custom Route');
        $this->addElement($use_custom_route);
        
        $custom_route = new Zend_Form_Element_Text('route');
        //$custom_route->setLabel('Custom Route');
        $this->addElement($custom_route);
        
        $desc = new Zend_Form_Element_Textarea('description');
        $desc->cols = 40;
        $desc->rows = 15;
        $desc->setLabel('Description');
        $desc->setRequired(false);
        $this->addElement($desc);

        parent::init();
    }
}