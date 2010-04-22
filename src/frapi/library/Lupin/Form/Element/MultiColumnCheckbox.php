<?php
/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 */

/** Zend_Form_Element_Multi */
// require_once 'Zend/Form/Element/Multi.php';

/**
 * MultiCheckbox form element
 *
 * Allows specifyinc a (multi-)dimensional associative array of values to use
 * as labelled checkboxes; these will return an array of values for those
 * checkboxes selected.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 */
class Lupin_Form_Element_MultiColumnCheckbox extends Zend_Form_Element_Multi
{
    /**
     * Use formMultiCheckbox view helper by default
     * @var string
     */
    public $helper = 'formMultiColumnCheckbox';

    /**
     * MultiCheckbox is an array of values by default
     * @var bool
     */
    protected $_isArray = true;
}
