<?php

/**
 * Action Sibling
 *
 * This is used to execute operations on a sibling resource. 
 * 
 * GET: Retrieve the information relative to a sibling
 * 
 * DELETE: Remove a sibling for this resource
 * 
 * HEAD: Retrieve the meta information relative to a sibling.
 * 
 *
 *
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /collection/:resource/siblings/:sibling
 */
class Action_Sibling extends Frapi_Action implements Frapi_Action_Interface
{

    /**
     * Required parameters
     *
     * @var An array of required parameters.
     */
    protected $requiredParams = array();

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
        throw new Frapi_Error('NO_POST_NOR_PUT');
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
        $this->data = array(
            'meta' => array(
                'total' => '1',
             ),
             'res1' => array(
                 'name' => 'res1',
                 'href' => '/collection/res1'
             )
         );

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
        $this->data = array(
            'success' => 1,
            'meta'    => array(
                'message' => 'The sibling has been deleted..'
            ),
        );

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
        $this->data = array(
            'meta' => array(
                'total' => '1',
            ),
        );
        return $this->toArray();
    }


}

