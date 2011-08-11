<?php
/**
 * Lupin SMS Clickatell package
 *
 * This is the package used to send SMS text messages using clickatell
 *
 * @package   Lupin
 */
class Lupin_SMS_Clickatell extends Lupin_SMS_Abstract
{
    /**
     * The url to query in order to send an sms.
     *
     * @var string  $url  The url of the webservice.
     */
    protected $url = 'http://api.clickatell.com/http/sendmsg?';

    /**
     * The username required by clickatell in order to send an sms.
     *
     * @var string $user  The user required.
     */
    protected $user = '';

    /**
     * The password required by clickatell in order to send an sms.
     *
     * @var string $password  The password required.
     */
    protected $password = '';

    /**
     * The api_id required by clickatell in order to send an sms.
     *
     * @var string $api_id  The api_id required.
     */
    protected $api_id = '';

    /**
     * Send a message
     *
     * This is the method invoked to send a message to a number.
     *
     * @param  string  $msg  The message to send.
     * @param  string  $to   The number to send it to.
     * @return bool          If the sending went well or not.
     */
    public function sendMessage($msg, $number)
    {
        $to = $number;
        $text = stripslashes($msg);

        $params = array(
            'user'     => $this->user,
            'password' => $this->password,
            'api_id'   => $this->api_id,
            'to'       => $to,
            'text'     => $text,
        );

        return $this->send($this->url, $params);
    }

    /**
     * Set parameters
     *
     * This method is used to set the parameters like the username
     * password, api_id, client_id, etc.
     *
     * @throws Exception if user, password or api_id isn't set.
     * @param  array $params A list of parameters associated to a driver.
     * @return void
     */
    function setParams(array $params)
    {
        if (!isset($params['user']) || !isset($params['password']) || !isset($params['api_id'])) {
            throw new Exception('Please have user, password and api_id set');
        }

        $this->user     = $params['user'];
        $this->password = $params['password'];
        $this->api_id   = $params['api_id'];
    }
}