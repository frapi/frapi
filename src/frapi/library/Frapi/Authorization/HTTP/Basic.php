
<?php
/**
 * Authorization by Basic Auth
 *
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
 * The authorization for partners.
 *
 * When doing anything about the partners, it
 * is going to be taking place here.
 *
 * @uses      Frapi_Authorization
 * @uses      Frapi_Authorization_Interface
 * @license   New BSD
 * @link      http://en.wikipedia.org/wiki/Basic_access_authentication
 * @package   frapi
 */
class Frapi_Authorization_HTTP_Basic extends Frapi_Authorization implements Frapi_Authorization_Interface
{

    /**
     * The name of the authentication realm
     *
     * @var string The authentication realm.
     */
    public $realm = 'FRAPI';

    /**
     * The base url of the application to authenticate against.
     *
     * @var string The base url for the authentication of the application.
     */
    public $baseUrl = '/';

    /**
     * Constructor
     *
     * The constructor that sets the $this->realm
     *
     * @param string $realm Perhaps a custom realm. Default is null so the
     *                      realm will be $_SERVER['SERVER_NAME']
     */
    public function __construct($realm = null)
    {
        $this->realm = $realm !== null ? $realm : $_SERVER['SERVER_NAME'];
    }

    /**
     * Send the Authentication Request
     *
     * This method is used to send the authentication
     * request the authentication header to the clients.
     *
     * @return void
     */
    public function send()
    {
        header(
            'WWW-Authenticate: Basic ' .
            'realm="' . $this->realm . '"'
        );

        header('HTTP/1.1 401 Unauthorized');
        echo 'HTTP Basic Authentication required for "' . $this->realm . '"';
        exit(0);
    }

    /**
     * Authorize the request
     *
     * This method is used to authorize the request. It fetches the
     * basic information from the request, decomposes it and finds out
     * the relevant information for authenticating the users.
     *
     * This method also makes use of Frapi_Model_Partner::isPartnerHandle()
     * to validate whether or not a user is a real user. If not then we bail
     * early.
     *
     * @link   http://www.peej.co.uk/projects/phphttpdigest.html
     *
     * @return mixed Either the username of the user making the request or we
     *               return access to $this->send() which will pop up the authentication
     *               challenge once again.
     */
    public function authorize()
    {
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            return $this->send();
        }

        $user = Frapi_Model_Partner::isPartnerHandle($_SERVER['PHP_AUTH_USER']);

        if ($user === false) {
            return $this->send();
        }

        if ($user['api_key'] == $_SERVER['PHP_AUTH_PW']) {
            return $_SERVER['PHP_AUTH_USER'];
        } else {
            return $this->send();
        }
    }

}
