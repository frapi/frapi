<?php
// require_once 'Zend/View/Helper/FormSelect.php';


/**
 * Helper to generate a set of checkbox button elements
 *
 * @category   Lupin
 * @package    Lupin_View
 * @subpackage Helper
 * @license    New BSD License
 */
class Lupin_View_Helper_FormMultiSelectBox extends Zend_View_Helper_FormSelect
{
    /**
     * Whether or not this element represents an array collection by default
     * @var bool
     */
    protected $_isArray = true;

    /**
     * Generates 'select' list of options.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The option value to mark as 'selected'; if an
     * array, will mark all values in the array as 'selected' (used for
     * multiple-select elements).
     *
     * @param array|string $attribs Attributes added to the 'select' tag.
     *
     * @param array $options An array of key-value pairs where the array
     * key is the radio value, and the array value is the radio text.
     *
     * @param string $listsep When disabled, use this list separator string
     * between list values.
     *
     * @return string The select tag and options XHTML.
     */
    public function formMultiSelectBox($name, $value = null, $attribs = null,
        $options = null, $listsep = "<br />\n")
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
            $opt     = isset($options[$i]) ? $options[$i][0] : '';
            $val     = isset($value[$i - 1]) ? $value[$i - 1] : '';
            $attrs   = isset($attribs[$i - 1]) ? $attribs[$i - 1] : '';
            $attrs['id'] =  substr($name, 0, -2) . $i;
            $string .= $this->formSelect($name, $val, $attrs, $opt);
            if ($i < $count) {
                $string .= '&nbsp;- &nbsp;';
            }
        }
        return $string;
    }
}