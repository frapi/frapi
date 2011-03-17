<?php

/**
 * Action Siblings
 *
 * This is a collection of sibling for a resource. This only accepts POST, DELETE,
 * GET and HEAD. 
 * 
 * POST: A post will add a new sibling using the name parameter
 * 
 * DELETE: This deletes the siblings connections, it removes all siblings in the
 * collection
 * 
 * GET: Retrieve a list of siblings and the relative information.
 * 
 * HEAD: Fetch the meta information for the siblings.
 *
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /collection/:resource/sibling
 */
class Action_Siblings extends Frapi_Action implements Frapi_Action_Interface
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
        throw new Frapi_Error('NO_PUT');
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
        $res = $this->getParam('resource', self::TYPE_OUTPUT);

        $this->data = array(
            'meta' => array(
                'total' => '2',
             ),
             'siblings' => array(
                 'res1' => array(
                     'name' => 'res1',
                     'href' => '/collection/' . $res . '/siblings/res1'
                 ),
                 'res2' => array(
                     'name' => 'res2',
                     'href' => '/collection/' . $res . '/siblings/res2'
                 )
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
        $required = $this->hasRequiredParameters(array(
            'name'
        ));

        if ($required instanceof Frapi_Error) {
            return $valid;
        }

        $name = $this->getParam('name', self::TYPE_OUTPUT);
        $resource = $this->getParam('resource', self::TYPE_OUTPUT);

        // When we create a new Resource, we return its new location
        // and the http code is 201 for Created.
        return new Frapi_Response(array(
            'code' => '201',
            'headers' => array(
                'Location' =>
                    '/collection/' . $resource . '/siblings/' . $name,
            ),
            'data' => array('success' => 1)
        ));

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
                'total' => '2',
             ),
        );

        return $this->toArray();
    }


}

