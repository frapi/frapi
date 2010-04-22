<?php
/**
 * This is taken from akrabat.com. 
 */
class Lupin_Validate_ValidFile extends Zend_Validate_Abstract
{

    const INI_SIZE = 'iniSize';     // Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini
    const FORM_SIZE = 'formSize';   // Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form. 
    const PARTIAL = 'partial';      // Value: 3; The uploaded file was only partially uploaded. 
    const NO_FILE = 'noFile';       // Value: 4; No file was uploaded. 
    const NO_TMP_DIR = 'noTmpDir';  // Value: 6; Missing a temporary folder.
    const CANT_WRITE = 'cantWrite'; // Value: 7; Failed to write file to disk.
    const EXTENSION = 'extension';  // Value: 8; File upload stopped by extension. Introduced in PHP 5.2.0. 
    const ERROR = 'error';          // General error for future proofing against new PHP versions

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
        self::FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
        self::PARTIAL => "The uploaded file was only partially uploaded",
        self::NO_FILE => "No file was uploaded",
        self::NO_TMP_DIR => "Missing a temporary folder",
        self::CANT_WRITE => "Failed to write file to disk",
        self::EXTENSION => "File upload stopped by extension",
        self::ERROR => "Unknown upload error"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value[error] is equal to UPLOAD_ERR_OK.
     * 
     * @note: This validator expects $value to be the array from $_FILES
     *
     * @param array $value
     * @return boolean
     */
    public function isValid($value)
    {
        // default value and error is "no file uploaded"
        $valueString = '';

        $error = UPLOAD_ERR_NO_FILE;
        
        if(is_array($value) && array_key_exists('error', $value)) {
            // set the error to the correct value
            $error = $value['error'];
            
            // set the %value% placeholder to the uplaoded filename
            $valueString = $value['name'];
        }
        
        $this->_setValue($valueString);

        $result = false;
        switch($error) {
            case UPLOAD_ERR_OK:
                $result = true;
                break;
                
            case UPLOAD_ERR_INI_SIZE:
                $this->_error(self::INI_SIZE);
                break;
                
            case UPLOAD_ERR_FORM_SIZE:
                $this->_error(self::FORM_SIZE);
                break;
                
            case UPLOAD_ERR_PARTIAL:
                $this->_error(self::PARTIAL);
                break;
                
            case UPLOAD_ERR_NO_FILE:
                $this->_error(self::NO_FILE);
                break;
                
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->_error(self::NO_TMP_DIR);
                break;
                
            case UPLOAD_ERR_CANT_WRITE:
                $this->_error(self::CANT_WRITE);
                break;
                
            case 8: // UPLOAD_ERR_EXTENSION isn't defined in PHP 5.1.4, so use the value
                $this->_error(self::EXTENSION);
                break;
                
            default:
                $this->_error(self::ERROR);
                break;
        }

        return $result;
    }

}

