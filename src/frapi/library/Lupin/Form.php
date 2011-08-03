<?php
/**
 */
class Lupin_Form extends Zend_Form
{
    /**
     * This is used for combobox values to represent NULL
     */
    const NULL_OPTION = '';

    /**
     * This can be overridden or reset after object construction
     *
     * @var string
     */
    protected $_method = Zend_Form::METHOD_POST;

    protected $_request;

    protected $_data = array();

    /**
     * Adds Cms/Form/Element to prefix path to be able to customise form
     * elements
     *
     * @param mixed $options Form configuration
     */
    public function __construct($options = null, $data = array())
    {
        $this->_request = Zend_Controller_Front::getInstance()->getRequest();
        $this->addPrefixPath('Lupin_Form_Element',  'Lupin/Form/Element',  'element');
        $this->addPrefixPath('Lupin_Form_Decorator','Lupin/Form/Decorator', 'decorator');

        foreach ($data as $name => $value) {
            $this->addData($name, $value);
        }

        parent::__construct($options);
    }

    /**
     * Initiallizes the Lupin Form Object. Sets the Lupin Composite decorator.
     * Remove default decorators from every element of the form.
     *
     * @return void
     */
    protected function prepare()
    {
        $this->setDisableLoadDefaultDecorators(true);

        $decorators = array(new Lupin_Form_Decorator_Composite);

        if ($this->_request->getParam('partial') !== 'true') {
            $options = array(
                'placement'           => 'prepend',
                'markupListStart'     => '<div class="form_errors">',
                'markupListEnd'       => '</div>',
                'markupListItemStart' => '',
                'markupListItemEnd'   => '<br />',
                'escape'              => true,
            );
            $errors = new Zend_Form_Decorator_FormErrors($options);
            $decorators[] = $errors;
        }

        $escape = new Zend_Form_Decorator_Description();
        $escape->setEscape(array('Lupin_Security', 'escape'));
        $decorators[] = $escape;


        $this->setDecorators($decorators);
        $this->setMethod($this->_method);
        $action = $this->getAction();
        if (empty($action)) {
            $this->setAction($this->_request->getPathInfo());
        }

        $id = $this->getId();
        if (empty($id)) {
            $this->setAttrib('id', $this->_request->getActionName());
        }

        // add a token check to all forms
        $this->addElement(new Zend_Form_Element_Hash('token', array('ignore' => true)));
    }

    protected function prepareElement($element)
    {
        if ($element->helper === 'formSelect') {
            $element->setRegisterInArrayValidator(false);
        }

        // Put a proper error message for setRequired(true)
        if ($element->isRequired()) {
            // Lets provide specific message for checkboxes
            if ($element->getType() === 'Lupin_Form_Element_MultiCheckbox' && $element->getValidator('NotEmpty') === false) {
                $element->addValidator('NotEmpty', true,
                    array(
                        'messages' => array(
                            'isEmpty'         => 'This field requires at least one checkbox to be selected.',
                        )
                    )
                );
            } else {
                if ($element->getValidator('NotEmpty') === false) {
                    $element->addValidator('NotEmpty', true,
                        array(
                            'messages' => array(
                                'isEmpty'         => 'This field requires a value but you submitted nothing.',
                            )
                        )
                    );
                }
            }
        }

        if ($val = $element->getValidator('Zend_Validate_EmailAddress')) {
            $val->setMessage("'%value%' is not a valid email address, please only submit a valid address.", 'emailAddressInvalidFormat');
        }

        // Only remove the default element decorators if we haven't been told
        // to preserve decorators
        if (!$element->preserveDecorators) {
            $this->_removeDefaultElementDecorators($element);
        }
    }

    /**
     * Removes all default decorators from a form element.
     *
     * @param Zend_Form_Element $element A form element
     *
     * @return void
     */
    protected function _removeDefaultElementDecorators(Zend_Form_Element $element)
    {
        $element->removeDecorator('HtmlTag');
        $element->removeDecorator('Label');
        $element->removeDecorator('FormElement');
        $element->removeDecorator('Errors');
        $element->removeDecorator('DtDdWrapper');
    }

    /**
     * Render form
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        $this->prepare();
        return parent::render($view);
    }

    /**
     * Add a new element
     *
     * $element may be either a string element type, or an object of type
     * Zend_Form_Element. If a string element type is provided, $name must be
     * provided, and $options may be optionally provided for configuring the
     * element.
     *
     * If a Zend_Form_Element is provided, $name may be optionally provided,
     * and any provided $options will be ignored.
     *
     * This function has been extended to allow us to call our specialized init
     * function which puts in the required decoration and also removes what
     * we don't need
     *
     * @param string|Zend_Form_Element $element Form element
     * @param string                   $name    Name of the element
     * @param array|Zend_Config        $options Options for the element
     *
     * @return Zend_Form                        The form object
     */
    public function addElement($element, $name = null, $options = null)
    {
        $this->prepareElement($element);
        return parent::addElement($element, $name, $options);
    }

    /**
     * Set default values for elements
     *
     * If an element's name is not specified as a key in the array, its value
     * is set to null.
     *
     * @param  array $defaults
     * @return Zend_Form
     */
    public function setDefaults(array $defaults)
    {
        // Deal with Lupin_Form_Element_Group exclusively
        foreach ($this->getElements() as $name => $element) {
            if ($element instanceof Lupin_Form_Element_Group) {
                foreach ($element->getElements() as $el) {
                    $elName = $el->getName();
                    if (array_key_exists($elName, $defaults)) {
                        $el->setValue($defaults[$elName]);
                        $element->addElement($el);
                    }
                }
            }
        }

        return parent::setDefaults($defaults);
    }

    public function addData($name, $data)
    {
        $this->_data[$name] = $data;
    }

    public function getData($name)
    {
        if (!isset($this->_data[$name])) {
            return null;
        }

        return $this->_data[$name];
    }

    public function clearData()
    {
        $this->_data = array();
    }
}
