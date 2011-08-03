<?php
// require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate a set of checkbox button elements
 *
 * @category   Lupin
 * @package    Lupin_View
 * @subpackage Helper
 * @license    New BSD License
 */
class Zend_View_Helper_FormStatic extends Zend_View_Helper_FormElement
{
    /**
     * Generates a set of textbox elements.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The checkbox value to mark as 'checked'.
     *
     * @param array $options An array of key-value pairs where the array
     * key is the checkbox value, and the array value is the radio text.
     *
     * @param array|string $attribs Attributes added to each radio.
     *
     * @return string The radio buttons XHTML.
     */
    public function formStatic($name, $value = null)
    {
        $info = $this->_getInfo($name, $value);
        extract($info); // name, value, attribs, options, listsep, disable
        return $value;
    }
}
