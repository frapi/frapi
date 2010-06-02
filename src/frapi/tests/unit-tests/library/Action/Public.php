<?php

/**
 * Action Public
 *
 * Dummy public action used for unit testing
 * 
 * @link http://getfrapi.com
 * @author Jeremy Kendall
 * @link /public
 */
class Action_Public extends Frapi_Action implements Frapi_Action_Interface
{
	/**
     * Required parameters
     * 
     * @var An array of required parameters.
     */
    protected $requiredParams = array('public_test');

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
        $this->data['public_test'] = $this->getParam('public_test', self::TYPE_OUTPUT);
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
        
        $this->data = array('action' => 'execute');
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
        
        $valid = $this->hasRequiredParameters($this->requiredParams);
        
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        $this->data = array('method' => 'get');
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
        
        $this->data = array('success' => 1, 'method' => 'post');
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
        $valid = $this->hasRequiredParameters($this->requiredParams);
        
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }
        
        $this->data = array('success' => 1, 'method' => 'put');
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
        
        if ($this->getParam('delete', self::TYPE_OUTPUT) == null) {
            throw new Frapi_Error('MISSING_PARAM', 'Required delete key is missing', 401);
        } else if ($this->getParam('delete') != '12') {
            throw new Frapi_Error('No record matches the provided key');
        }
        
        $this->data = array('success' => 1);
        
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
        
        $this->data = array('head' => 'meta-data');
        
        return $this->toArray();
    }

}
