<?php
/**
 * Frapi_Response
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
 * @TODO Add the setContentType, etc etc.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @since     0.2.0
 * @package   frapi
 */
class Frapi_Response
{
    /**
     * HTTP Response Code
     *
     * @var int
     */
    protected $http_code = 200;

    /**
     * Content Type
     *
     * @var string
     */
    protected $content_type = false;

    /**
     * The data to use in the output
     *
     * This is the response data.
     *
     * @var array An array of data.
     */
    protected $data = array();

    /**
     * The constructor
     *
     * The constructor takes on a single parameter which is
     * an array of response information
     *
     * Up to version 0.0.1 the only 2 things the response array
     * contains is "code" and "data".
     *
     * Ex:
     * <?php
     *     return new Frapi_Response(array(
     *         'code' => '201',
     *         'data' => array('location' => '/resource/id')
     *         'headers' => array('X-Content-Love' => 'Nothing')
     *     ));
     * ?>
     *
     * @param array $response An array of code, data and response information
     */
    public function __construct(array $response)
    {
        if (isset($response['code'])) {
            $this->http_code = $response['code'];
        }

        if (isset($response['data'])) {
            $this->data = $response['data'];
        }

        if (isset($response['headers']) && is_array($response['headers'])) {
            foreach ($response['headers'] as $header => $value) {
                if (is_int($header)) {
                    header($value);
                } elseif (isset($value)) {
                    header($header . ': ' . $value);
                }
            }
        }
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
     * Set the Status code
     *
     * This method is used to set the HTTP status
     * code of the response we are going to return.
     *
     * @param int $code The http status code.
     * @return void
     */
    public function setStatusCode($code)
    {
        $this->http_code = $code;
    }

    /**
     * Get the data
     *
     * This method gets the data variable to be used in the output.
     *
     * @return array An array of data to use in the output.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data
     *
     * This method sets the data variable to be used in the output.
     *
     * @param  array $data An array of data to set in the response.
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the content type
     *
     * This method gets the content type for the return data
     *
     * @return string A valid mimetype
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * Set the content type
     *
     * This method sets the content type for the return data
     *
     * @param string A valid mimetype
     */
    public function setContentType($mimetype)
    {
        $this->content_type = $mimetype;
    }
}
