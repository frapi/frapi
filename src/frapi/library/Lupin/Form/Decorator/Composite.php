<?php

class Lupin_Form_Decorator_Composite extends Zend_Form_Decorator_Abstract
{
    /**
     * Returns action path
     *
     * @return string   The action path for the form action attribute
     */
    public function buildAction()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $action  =  $request->getBaseUrl() .
             '/' . $request->getModuleName() .
             '/' . $request->getControllerName() .
             '/' . $request->getActionName();
        $id = $request->getParam('id');
        if (!is_null($id)) {
            $action .= '/id/' . $id;
        }

        return $action;
    }

    /**
     * Returns form attributes
     *
     * @return string The rendered attributes of the form
     */
    public function buildFormAttributes()
    {
        $element    = $this->getElement();
        $attributes = array();
        $output     = '';
        if ($element instanceof Zend_Form) {
            $attributes = $element->getAttribs();
            if (!array_key_exists('action', $attributes)) {
                $attributes['action'] = $this->buildAction();
            }

            foreach ($attributes as $attributeName => $attributeValue) {
                $output .= " $attributeName=\"$attributeValue\"";
            }
        }

        return $output;
    }

    /**
     * Iterates through all form elements, rendering each and every one of them
     * using the default view. This method also passes to the view all necessary
     * variables (name, label, element, error)
     *
     * @return string The rendered elements of the form. The skeleton of the
     *                form elements as its styles and classes can be set in
     *                its view at 'form/element.phtml'.
     */
    public function buildElements()
    {
        $elements = $this->getElement();
        $output   = '';

        if (!empty($elements)) {
            foreach ($elements as $element) {
                if (!($element instanceof Zend_Form_Element)) {
                    continue;
                }

                // make sure view variables are not passed between cycles
                $view = clone $element->getView();

                // initialize view variables
                $view->id         = $element->getId();
                $view->name       = $element->getName();
                $view->label      = '';
                $view->labelClass = '';
                $view->element    = '';
                $view->error      = '';

                // buttons do not require a label
                if (!in_array($element->getType(), array(
                    'Zend_Form_Element_Submit',
                    'Zend_Form_Element_Button',
                    'Zend_Form_Element_Reset',
                    'Zend_Form_Element_Hash',
                    'Zend_Form_Element_Hidden',
                ))) {
                    $view->label      = $element->getLabel();
                    $view->labelClass = $element->getAttrib('label_class');
                }

                $view->required = $element->isRequired();

                // fixes the missing list separator
                if ($element instanceof Zend_Form_Element_Multi) {
                    $element->setSeparator($element->getAttrib('listsep'));
                }

                // render from the default helper
                $view->element = $element->render() . PHP_EOL;

                // fetches message into error view variable
                $messages = $element->getMessages();
                if (!empty($messages)) {
                    foreach ($messages as $code => $message) {
                        if (strlen($message) === 0) {
                            unset($messages[$code]);
                        }
                    }

                    $view->error = implode(',', $messages);
                }

                // adds the element source to the output
                $elementOutput = '';
                if ($element->preserveDecorators) {
                    $elementOutput .= $view->element;
                } elseif (
                       $element instanceof Zend_Form_Element_Hidden
                    || $element instanceof Zend_Form_Element_Hash
                ) {
                    $elementOutput .= $view->element;
                } elseif (
                       $element instanceof Zend_Form_Element_Checkbox
                    || $element instanceof Zend_Form_Element_MultiCheckbox
                    || $element instanceof Lupin_Form_Element_MultiCheckbox
                ) {
                    if ($view->getScriptPath('form/element/checkboxes.phtml') !== false) {
                        $view->singleCheckbox = false;
                        if ($element instanceof Zend_Form_Element_Checkbox) {
                            $view->singleCheckbox = true;
                        }

                        $elementOutput .= $view->render('form/element/checkboxes.phtml');
                    }
                } elseif (
                       $element instanceof Zend_Form_Element_Button
                    || $element instanceof Zend_Form_Element_Submit
                ) {
                    if ($view->getScriptPath('form/element/buttons.phtml') !== false) {
                        $elementOutput .= $view->render('form/element/buttons.phtml');
                    }
                }

                if ($elementOutput === '') {
                    $elementOutput .= $view->render('form/element.phtml');
                }

                $output .= $elementOutput;
            }
        }

        return $output;
    }

    /**
     * Renders the form. The form is rendered from a view called form.phtml.
     * The skeleton of the form as its styles and classes can be set in the
     * view.
     *
     * @param string $content Content to be rendered
     *
     * @return string The rendered form
     */
    public function render($content)
    {
        $form = $this->getElement();
        if (!$form instanceof Zend_Form) {
            return '';
        }

        $view   = $this->getElement()->getView();
        $view->formAttributes = $this->buildFormAttributes();
        $view->formElements   = $this->buildElements();
        $output = $view->render('form.phtml');

        // form tag is intentionally not closed
        // to allow for extra fields

        return $output;
    }
}