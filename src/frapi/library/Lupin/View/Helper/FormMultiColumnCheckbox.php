<?php
/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */


/**
 * Abstract class for extension
 */
// require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate a set of multi column checkbox button elements
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Lupin_View_Helper_FormMultiColumnCheckbox extends Zend_View_Helper_FormElement
{
    /**
     * Input type to use
     * @var string
     */
    protected $_inputType = 'checkbox';

    /**
     * Whether or not this element represents an array collection by default
     * @var bool
     */
    protected $_isArray = true;

    /**
     * Generates a set of radio button elements.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The radio value to mark as 'checked'.
     *
     * @param array $options An array of key-value pairs where the array
     * key is the radio value, and the array value is the radio text.
     *
     * @param array|string $attribs Attributes added to each radio.
     *
     * @return string The radio buttons XHTML.
     */
    public function formMultiColumnCheckbox($name, $value = null, $attribs = null,
        $options = null, $listsep = "<br />\n")
    {

        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, value, attribs, options, listsep, disable

        // retrieve attributes for labels (prefixed with 'label_' or 'label')
        $label_attribs = array();
        foreach ($attribs as $key => $val) {
            $tmp    = false;
            $keyLen = strlen($key);
            if ((6 < $keyLen) && (substr($key, 0, 6) == 'label_')) {
                $tmp = substr($key, 6);
            } elseif ((5 < $keyLen) && (substr($key, 0, 5) == 'label')) {
                $tmp = substr($key, 5);
            }

            if ($tmp) {
                // make sure first char is lowercase
                $tmp[0] = strtolower($tmp[0]);
                $label_attribs[$tmp] = $val;
                unset($attribs[$key]);
            }
        }

        $labelPlacement = 'append';
        foreach ($label_attribs as $key => $val) {
            switch (strtolower($key)) {
                case 'placement':
                    unset($label_attribs[$key]);
                    $val = strtolower($val);
                    if (in_array($val, array('prepend', 'append'))) {
                        $labelPlacement = $val;
                    }
                    break;
            }
        }

        // the radio button values and labels
        $options = (array) $options;

        // build the element
        $xhtml = '';
        $list  = array();

        // should the name affect an array collection?
        $name = $this->view->escape($name);
        if ($this->_isArray && ('[]' != substr($name, -2))) {
            $name .= '[]';
        }

        // ensure value is an array to allow matching multiple times
        $value = (array) $value;

        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
            $endTag= '>';
        }

        // add radio buttons to the list.
        // require_once 'Zend/Filter/Alnum.php';
        $filter = new Zend_Filter_Alnum();

        $column_count = 3;
        $count        = count($options);
        $perColumn    = (int)ceil($count / $column_count);
        $i = $j       = 0;
        $columns      = array();

        foreach ($options as $opt_value => $opt_label) {
            // Should the label be escaped?
            if ($escape) {
                $opt_label = $this->view->escape($opt_label);
            }

            // is it disabled?
            $disabled = '';
            if (true === $disable) {
                $disabled = ' disabled="disabled"';
            } elseif (is_array($disable) && in_array($opt_value, $disable)) {
                $disabled = ' disabled="disabled"';
            }

            // is it checked?
            $checked = '';
            if (in_array($opt_value, $value)) {
                $checked = ' checked="checked"';
            }

            // generate ID
            $optId = $id . '-' . $filter->filter($opt_value);

            // Wrap the radios in labels
            $radio = '<label class="newline" '
                    . $this->_htmlAttribs($label_attribs) . ' for="' . $optId . '">'
                    . (('prepend' == $labelPlacement) ? $opt_label : '')
                    . '<input type="' . $this->_inputType . '"'
                    . ' name="' . $name . '"'
                    . ' id="' . $optId . '"'
                    . ' value="' . $this->view->escape($opt_value) . '"'
                    . $checked
                    . $disabled
                    . $this->_htmlAttribs($attribs)
                    . $endTag
                    . (('append' == $labelPlacement) ? $opt_label : '')
                    . '</label>';

            if ($perColumn === $i && $j < $column_count) {
                $i = 0;
                $j++;
                $count = $count - $perColumn;
            }

            $columns[$j][] = $radio;
            $i++;
        }

        // done!
        $xhtml .= '<div style="clear: both;">' ."\n";
        for ($i = 0; $i < count($columns); $i++) {
            $xhtml .= '<div style="width: 30%; float: left;">' . "\n";
            $xhtml .= implode($listsep, $columns[$i]);
            $xhtml .= '</div>' . "\n\n";
        }
        $xhtml .= "</div>\n";

        return $xhtml;
    }
}
