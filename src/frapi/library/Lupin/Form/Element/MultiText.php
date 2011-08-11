<?php
// require_once 'Zend/Form/Element/Text.php';

/**
 * MultiText form element
 *
 * Allows specifyinc a (multi-)dimensional associative array of values to use
 * as textbox; these will return an array of values for those
 * textboxes selected.
 *
 * Allows you to achieve:
 * Label: [box] - [box] - [box]
 *
 * @category   Lupin
 * @package    Lupin_Form
 * @subpackage Element
 */
class Lupin_Form_Element_MultiText extends Zend_Form_Element_Text
{
    /**
     * Use formMultiText view helper by default
     * @var string
     */
    public $helper = 'formMultiText';

    /**
     * MultiCheckbox is an array of values by default
     * @var bool
     */
    protected $_isArray = true;
}
