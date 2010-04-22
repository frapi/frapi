<?php
// require_once 'Zend/Form/Element/Xhtml.php';

/**
 * Static form element
 *
 * This element will allow you to input any HTML or markup
 * and it will be displayed as is.
 *
 * @category   echolibre
 * @package    Lupin_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2009 echolibre
 */
class Lupin_Form_Element_Static extends Zend_Form_Element_Xhtml
{
    /**
     * Use formMultiText view helper by default
     * @var string
     */
    public $helper = 'formStatic';
}
