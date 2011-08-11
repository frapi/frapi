<?php
/**
 * Groups together multiple elements
 *
 * @category   Lupin
 * @package    Lupin_Form
 * @subpackage Element
 */
class Lupin_Form_Element_Group extends Zend_Form_Element_Xhtml
{
    /**
     * Separator to use between elements; defaults to '&nbsp;'.
     * @var string
     */
    protected $_separator = '&nbsp;';

    /**
     * Contains all the elements to group together
     * @var array
     */
    protected $_elements = array();


    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $this->addDecorator('Group');
    }

    public function addElement(Zend_Form_Element $element)
    {
        $this->_elements[$element->getName()] = $element;
    }

    public function setElements(array $elements)
    {}

    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Retrieve separator
     *
     * @return mixed
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * Set separator
     *
     * @param mixed $separator
     * @return self
     */
    public function setSeparator($separator)
    {
        $this->_separator = $separator;
        return $this;
    }

    public function getLabel()
    {
        $label = $this->_label;
        if (empty($label)) {
            $elements = array_values($this->getElements());
            $label    = $elements[0]->getLabel();
            $this->setLabel($label);
        }

        return $label;
    }

    public function isRequired()
    {
        $elements = $this->getElements();
        if (empty($elements)) {
            return parent::isRequired();
        }

        foreach ($elements as $el) {
            if ($el->isRequired()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Override isValid()
     *
     * Ensure that validation rules for each element is ran.
     *
     * @param  string $value
     * @param  mixed $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        foreach ($this->getElements() as $element) {
            if (!isset($context[$element->getName()])) {
                continue;
            }

            $valid = $element->isValid($context[$element->getName()], $context);
            if ($valid === false) {
                $this->_messages = array_merge($this->_messages, $element->getMessages());
                $this->_errors   = array_merge($this->_errors,   $element->getErrors());
                return false;
            }
        }

        return true;
    }
}
