<?php

/**
 * Action Testing1 
 * 
 * Testing 1 edit
 * 
 * @link http://echolibre.com/frapi
 * @author Echolibre <frapi@echolibre.com>
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

    public function executeAction()
    {
        throw new Frapi_Error("> 'OH_NO_REALLY'", 'THIS IS NOT COOL', 305);
        throw new Frapi_Error("NO_CODE", 'THIS IS NOT COOL');
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }
        
        return $this->toArray();
    }

    public function executeGet()
    {
        $cache = Frapi_Cache::getInstance();
        $b = $this->getParam('bazinga', self::TYPE_OUTPUT);
        
        return $this->toArray();
    }

    public function executePost()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }
        
        return $this->toArray();
    }

    public function executePut()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }
        
        return $this->toArray();
    }

    public function executeDelete()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }
        
        return $this->toArray();
    }


}

