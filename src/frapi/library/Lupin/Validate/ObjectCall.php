<?php
/**
 *
 */
class Lupin_Validate_ObjectCall extends Zend_Validate_Abstract
{
    const METHOD_CALL_RETURN_FAILURE    = 'methodReturnedFailure';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::METHOD_CALL_RETURN_FAILURE => "Trying to validate '%value%' failed",
    );

    protected $object;
    protected $method = '';
    protected $badReturn = false;
    protected $data = array();

    public function __construct($object, $method)
    {
        $this->setObject($object)->setMethod($method);
    }

    public function setObject($object)
    {
        if (!is_object($object)) {
            throw new Zend_Validate_Exception('Internal error: Passed parameter is not an object');
        }

        $this->object = $object;
        return $this;
    }

    public function setMethod($method)
    {
        if (is_object($this->object) && !method_exists($this->object, $method)) {
            throw new Zend_Validate_Exception('Internal error: Passed parameter is not a valid method in object' . get_class($this->object));
        }

        $this->method = $method;
        return $this;
    }

    public function setBadReturn($value)
    {
        $this->badReturn = $value;
    }

    public function getBadReturn()
    {
        return $this->badReturn;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        if (empty($this->object) || empty($this->method)) {
            throw new Zend_Validate_Exception('Internal error: Both the object and method name need to be set');
        }

        $this->_setValue($value);
        $values = array($value);

        foreach ($this->data as $data) {
            $values[] = $data;
        }

        if (call_user_func_array(array($this->object, $this->method), $values) === $this->getBadReturn()) {
            $this->_error(self::METHOD_CALL_RETURN_FAILURE);
            return false;
        }

        return true;
    }
}