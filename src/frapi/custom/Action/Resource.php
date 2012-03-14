<?php

/**
 * Action Resource
 *
 * This is the resource contained in a certain collection group. 
 * 
 * PUT: A put with the "name" parameter will update the name of the resource
 * 
 * DELETE: Deletes the resource.
 * 
 * GET: This retrieves the information relative to the resource contained in the
 * collection
 *
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /collection/:resource
 */
class Action_Resource extends Frapi_Action implements Frapi_Action_Interface
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
        throw new Frapi_Error('NO_POST');
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
                'name' => $res
            ),
            'siblings' => array(
                'res1' => array(
                    'href' => '/collection/' . $res . '/siblings/res1',
                    'meta' => array('name' => 'res1')
                )
            ),
        );

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
        // We fake updating the data here.
        $this->data = array(
            'success' => 1,
            'meta' => array(
                'message' => 'Resource updated.'
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
        // Instead of a successful delete here we'll return
        // a failed deletion because the resource would no
        // longer exist.
        throw new Frapi_Error('RESOURCE_NOT_FOUND');
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
                'name' => $this->getParam('resource', self::TYPE_OUTPUT)
            )
        );

        return $this->toArray();
    }

    /**
     * Custom DOCS request handler.
     *
     * This method is called when a non-conventional DOCS
     * request is made.
     *
     * @return array
     */
    public function executeDocs()
    {
        return new Frapi_Response(array(
            'code' => 200,
            'data' => array(
                'GET'    => 'This retrieves the information relative ' .
                            'to the resource contained in the collection',
                'DELETE' => 'Deletes the resource.',
                'PUT'    => 'A put with the "name" parameter will update ' .
                            'the name of the resource'
            )
        ));
    }
}
