<?php
/** Zend_View_Helper_FormText */
// require_once 'Zend/View/Helper/FormText.php';


/**
 * Helper to generate a set of checkbox button elements
 *
 * @category   Lupin
 * @package    Lupin_View
 * @subpackage Helper
 * @license    New BSD License
 */
class Zend_View_Helper_FormMultiText extends Zend_View_Helper_FormText
{
    /**
     * Input type to use
     * @var string
     */
    protected $_inputType = 'text';

    /**
     * Whether or not this element represents an array collection by default
     * @var bool
     */
    protected $_isArray = true;

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
    public function formMultiText($name, $value = null, $attribs = null, $separator = '&nbsp;- &nbsp;')
    {
        if (!isset($attribs['count']) || $attribs['count'] < 1) {
            $attribs['count'] = 1;
        }

        $count = $attribs['count'];
        unset($attribs['count']);

        if (!is_array($value) || !isset($value[0])) {
            $value = array($value);
        }

        if (!is_array($attribs) || !isset($attribs[0])) {
            $attribs = array($attribs);
        }

        $string = '';
        for ($i = 1; $i <= $count; $i++) {
            $val     = isset($value[$i - 1]) ? $value[$i - 1] : '';
            $attrs   = $attribs[$i - 1];
            $attrs['id'] = substr($name, 0, -2) . $i;
            $string .= $this->formText($name, $val, $attrs);
            if ($i < $count) {
                $string .= $separator;
            }
        }
        return $string;
    }
}
