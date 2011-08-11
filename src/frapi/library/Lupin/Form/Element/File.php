<?php
// require_once 'Zend/Form/Element/Xhtml.php';
/**
 *
 * @category   Lupin
 * @package    Lupin_Form
 * @subpackage Element
 */
class Lupin_Form_Element_File extends Zend_Form_Element_Xhtml
{
    /**
     * Flag indicating whether or not to insert ValidFile validator when element is required
     * @var bool
     */
    protected $_autoInsertValidFileValidator = true;

    /**
     * Default view helper to use
     * @var string
     */
    public $helper = 'formFile';

    /**
     * Set flag indicating whether a ValidFile validator should be inserted when element is required
     *
     * @param  bool $flag
     * @return Zend_Form_Element
     */
    public function setAutoInsertValidFileValidator($flag)
    {
        $this->_autoInsertValidFileValidator = (bool) $flag;
        return $this;
    }

    /**
     * Get flag indicating whether a ValidFile validator should be inserted when element is required
     *
     * @return bool
     */
    public function autoInsertValidFileValidator()
    {
        return $this->_autoInsertValidFileValidator;
    }

    public function isValid($value, $context = null)
    {
        // for a file upload, the value is not in the POST array, it's in $_FILES
        $key = $this->getName();
        if($value === null) {
            if(isset($_FILES[$key])) {
                $value = $_FILES[$key];
            }
        }

        // auto insert ValidFile validator
        if (
            $this->isRequired()
            && $this->autoInsertValidFileValidator()
            && !$this->getValidator('ValidFile')
        ) {
            $validators = $this->getValidators();
            $validFile   = array('validator' => 'ValidFile', 'breakChainOnFailure' => true);
            array_unshift($validators, $validFile);
            $this->setValidators($validators);

            // do not use the automatic NotEmpty Validator as ValidFile replaces it
            $this->setAutoInsertNotEmptyValidator(false);
        }

        return parent::isValid($value, $context);
    }
}
