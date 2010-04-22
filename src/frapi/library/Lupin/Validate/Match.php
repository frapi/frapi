<?php

class Lupin_Validate_Match extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'MatchNotMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'The fields do not match.'
    );

    /**
     * The fields that the current element needs to match
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * The argument to this constructor is the third argument to the elements' addValidator
     * method.
     *
     * @param array|string $fieldsToMatch
     */
    public function __construct($fields = array())
    {
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $this->_fields[] = (string)$field;
            }
        } else {
            $this->_fields[] = (string)$fields;
        }
    }

    /**
     * Check if the element using this validator is valid
     *
     * This method will compare the $value of the element to the other elements
     * it needs to match. If they all match, the method returns true.
     *
     * @param $value string
     * @param $context array All other elements from the form
     * @return boolean Returns true if the element is valid
     */
    public function isValid($value, $context = null)
    {
        $value = (string)$value;
        $this->_setValue($value);

        foreach ($this->_fields as $field) {
            if (!isset($context[$field]) || $value !== $context[$field]) {
                $this->_error(self::NOT_MATCH);
                return false;
            }
        }

        return true;
    }
}