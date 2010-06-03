<?php
/**
 * First developer to find this gets a free drink. Report a bug on
 * http://github.com/frapi/frapi about it :)
 *
 * Drink has been awarded to Jeremy Kendall
 * @link http://github.com/frapi/frapi/issues/issue/16
 */
if (!defined('EXTRA_LIBRARIES_ROOT_PATH')) {
    require 'Zend/Auth/Adapter/Interface.php';
} else {
    require EXTRA_LIBRARIES_ROOT_PATH . 'Zend/Auth/Adapter/Interface.php';
}

/**
 * Authorization Interface
 *
 * The interface with the methods that
 * each Authorization must implement.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://getfrapi.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@getfrapi.com so we can send you a copy immediately.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */

class Frapi_Authorization_Adapter_Xml implements Zend_Auth_Adapter_Interface
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.  This method is called to
     * attempt an authenication.  Previous to this call, this adapter would have already
     * been configured with all nessissary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     *
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $model    = new Default_Model_User();
        
        $result = false;
        foreach ($model->getAll() as $key => $user) {
            if ($user['handle'] == $this->_identity && $user['password'] == $this->_credential) {
                $result = (object)$user;
            }
        }
        
        $code     = Zend_Auth_Result::FAILURE;
        $messages = array();

        if ($result === false || $result->active === 0) {
            $code       = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $messages[] = 'A record with the supplied identity could not be found.';
        } elseif ($this->_credential !== $result->password) {
            $code       = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $messages[] = 'Supplied credential is invalid.';
        } else {
            unset($result->password);
            $this->_resultRow = $result;
            $code       = Zend_Auth_Result::SUCCESS;
            $messages[] = 'Authentication successful.';
        }

        return new Zend_Auth_Result($code, $this->_identity, $messages);
    }

    /**
     * setIdentity() - set the value to be used as the identity
     *
     * @param  string $value
     * @return Zend_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setIdentity($value)
    {
        $this->_identity = $value;
        return $this;
    }

    /**
     * setCredential() - set the credential value to be used
     *
     * @param  string $credential
     * @return Zend_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setCredential($credential)
    {
        $this->_credential = $credential;
        return $this;
    }

    /**
     * Returns the result row as a stdClass object
     *
     * @return stdClass|boolean
     */
    public function getResult()
    {
        if (!$this->_resultRow) {
            return false;
        }

        return $this->_resultRow;
    }
}
