<?php
/**
 * Lupin SMS Abstract
 *
 * Abstract class where all the methods are declared
 * and the main send method is.
 *
 * @package   Lupin
 */
abstract class Lupin_SMS_Abstract
{
    /**
     * Make the HTTP request
     *
     * This method will create the REST HTTP Request and
     * place it on the server building a list of query
     * parameters using the associative array passed in params.
     *
     * @param  string $url    The URL of the webservice.
     * @param  array  $params An array of query parameters.
     *
     * @return bool   $res    If you have all good it'll return true else it
     *                        should be false.
     */
    public static function send($url, $params)
    {
        $query = http_build_query($params);
        $res   = file_get_contents($url . $query);

        return $res;
    }

    /**
     * Abstract method sendMessage
     *
     * This method should be implemented in all driver in the Lupin_SMS
     * package.
     *
     * @param string  $msg  The message to send.
     * @param string  $to   The number to send it to.
     */
    abstract function sendMessage($msg, $to);

    /**
     * Set parameters
     *
     * This method is used to set the parameters like the username
     * password, api_id, client_id, etc.
     *
     * @param array $params A list of parameters associated to a driver.
     */
    abstract function setParams(array $params);
}
