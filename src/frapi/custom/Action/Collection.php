<?php

/**
 * Action Collection
 *
 * This is an example of a collection. A collection is a bucket of resources. In
 * this case, you can only POST and DELETE this collection.
 *
 * POST: A post will add a new resource to a collection. You have to pass a "name"
 * parameter.
 *
 * DELETE: Delete collection will remove the collection of resources from the
 * system.
 *
 * Try it with "curl -X POST http://api.frapi/collection -d '{"name":"new"}' -H
 * 'Content-Type: application/json'
 *
 *
 *
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /collection
 */
class Action_Collection extends Frapi_Action implements Frapi_Action_Interface
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

    public function __construct()
    {
        require_once CUSTOM_MODEL . DIRECTORY_SEPARATOR . 'Auth.php';

        $auth = new Custom_Model_Auth();
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
        $resources = array(
            'meta' => array(
                'total' => 'N',
                'desc'  => 'The total should be the active resources ' .
                           'contained in a collection/bucket.',
            ),
            'resources' => array(
                'res1' => array(
                    'href' => '/collection/res1',
                    'name' => 'res1',
                ),

                'res2' => array(
                    'href' => '/collection/res2',
                    'name' => 'res2',
                ),

                'res3' => array(
                    'href' => '/collection/res3',
                    'name' => 'res3',
                )
            ),
        );

        $this->data = $resources;
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

        // When we create a new Resource, we return its new location
        // and the http code is 201 for Created.
        return new Frapi_Response(array(
            'code' => '201',
            'headers' => array(
                'Location' => '/collection/' . $name,
            ),
            'data' => array('success' => 1)
        ));
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
                'message' => 'The entire collection has been deleted with success.'
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
        $resources = array(
            'meta' => array(
                'total' => 'N',
                'desc'  => 'The total should be the active resources ' .
                           'contained in a collection/bucket.'
            )
        );

        $this->data = $resources;
        return $this->toArray();
    }
}
