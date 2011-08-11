<?php
// require_once 'Zend/Form/Element/Select.php';

/**
 * MultiSelect form element
 *
 * Allows specifyinc a (multi-)dimensional associative array of values to use
 * as selectboxes; these will return an array of values for those
 * selectboxes selected.
 *
 * Allows you to achieve:
 * Label: [select] - [select] - [select]
 *
 * @category   Lupin
 * @package    Lupin_Form
 * @subpackage Element
 */
class Lupin_Form_Element_MultiSelectBox extends Zend_Form_Element_Select
{
    /**
     * Use formMultiSelect view helper by default
     * @var string
     */
    public $helper = 'formMultiSelectBox';

    /**
     * MultiSelect is an array of values by default
     * @var bool
     */
    protected $_isArray = true;
}
