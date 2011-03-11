<?php

/**
 * Action Testing1
 *
 * Testing 1 edit2
 *
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /testing/1
 */
class Action_Testing1 extends Frapi_Action implements Frapi_Action_Interface
{

    /**
     * Required parameters
     *
     * @var An array of required parameters.
     */
    protected $requiredParams = array('bazinga');

    /**
     * The data container to use in toArray()
     *
     * @var A container of data to fill and return in toArray()
     */
    private $data = array();

    /**
     * To Array
     *
     * This method returns the value found in the database
     * into an associative array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Default Call Method
     *
     * This method is called when no specific request handler has been found
     *
     * @return array
     */
    public function executeAction()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        return $this->toArray();
    }

    /**
     * Get Request Handler
     *
     * This method is called when a request is a GET
     *
     * @return array
     */
    public function executeGet()
    {
        // Access your API like: http://api.frapi/testing/1.printr?bazinga=2.3david"><script>2
        $this->data['bazinga-escaped'] = $this->getParam('bazinga', self::TYPE_OUTPUT);
        $this->data['bazinga-plain']   = $this->getParam('bazinga', self::TYPE_STRING);
        $this->data['bazinga-int']     = $this->getParam('bazinga', self::TYPE_INT);
        $this->data['bazinga-float']   = $this->getParam('bazinga', self::TYPE_FLOAT);

        $this->data['_name'] = 'david';

        $this->data['objects'] = array(
            'object' => array(
                'name'  => array(),
                'name2' => array()
            )
        );

        return $this->toArray();
    }

    /**
     * Post Request Handler
     *
     * This method is called when a request is a POST
     *
     * @return array
     */
    public function executePost()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        throw new Frapi_Error('TestingPost', 'POST Error');
        // This instantiates an ArmChair object to access CouchDB. If you need
        // something more advanced well... use something else, it's not forbidden.
        // $chair = new ArmChair('http://localhost:5984/databasenamehere');

        return $this->toArray();
    }

    /**
     * Put Request Handler
     *
     * This method is called when a request is a PUT
     *
     * @return array
     */
    public function executePut()
    {
        $auth = new Custom_Model_Auth();

        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        return $this->toArray();
    }

    /**
     * Delete Request Handler
     *
     * This method is called when a request is a DELETE
     *
     * @return array
     */
    public function executeDelete()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        return $this->toArray();
    }

    /**
     * Head Request Handler
     *
     * This method is called when a request is a HEAD
     *
     * @return array
     */
    public function executeHead()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        return $this->toArray();
    }


}

