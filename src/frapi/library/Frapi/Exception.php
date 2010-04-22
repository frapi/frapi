<?php
/**
 * Frapi_Exception
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
class Frapi_Exception extends Exception
{
    /**
     * Where the error happened
     *
     * @var string $at  Where did that error actually happen?
     */
    protected $at;
    
    /**
     * HTTP Response Code
     *
     * @var int Default to 400 if nothing was mentionned ;-)
     */
    protected $http_code = 400;
    
    /**
     * The name of the actual exception
     *
     * @var string The name of the exception
     */
    protected $name;
    
    public function __construct($message, $name, $http_code = 400, $at = '')
    {
        // make sure everything is assigned properly
        parent::__construct($message, $http_code);
        
        $this->at        = $at;
        $this->name      = $name;
        $this->http_code = $http_code;
    }

    /**
     * If someone is calling echoing this object directly
     * we will output the message that has been set.
     *
     * @return string $this->message  The message
     */
    public function __toString()
    {
        return $this->message;
    }

    public function getAt()
    {
        return $this->at;
    }
   
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get HTTP status code for this error
     *
     * @return Int
     */
    public function getStatusCode()
    {
        return $this->http_code;
    }

    /**
     * This is a simple ugly and stupid function
     * to format the array (of errors) in the format
     * that I wish
     *
     * @return array The formatted error array
     */
    public function getErrorArray()
    {
        return array(
            'errors' => array(
                array(
                    'message' => $this->getMessage(),
                    'name'    => $this->getName(),
                    'at'      => $this->getAt()
                )
            )
        );
    }
}
