<?php

class Lupin_Form_Decorator_Group extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Lupin_Form_Element_Group) {
            return $content;
        }

        $view = $element->getView();
        if (!$view instanceof Zend_View_Interface) {
            // using view helpers, so do nothing if no view present
            return $content;
        }

        $elements = $element->getElements();
        if (empty($elements)) {
            return $content;
        }

        $markup = array();
        foreach ($elements as $el) {
            if (method_exists($el, 'getOptions')) {
                $options = $el->getOptions();
            } else if (method_exists($el, 'getMultiOptions')) {
                $options = $el->getMultiOptions();
            } else {
                $options = array();
            }

            $markup[] = $view->{$el->helper}($el->getName(), $el->getValue(), $el->getAttribs(), $options);
        }

        $markup = implode($element->getSeparator(), $markup);
        switch ($this->getPlacement()) {
            case self::PREPEND:
                return $markup . $this->getSeparator() . $content;
            case self::APPEND:
            default:
                return $content . $this->getSeparator() . $markup;
        }
    }
}