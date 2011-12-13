<?php
/**
 * Authorization Digest Challenge + Logic
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
 * @link      http://www.peej.co.uk/projects/phphttpdigest.html
 * @link      http://en.wikipedia.org/wiki/Digest_access_authentication
 * @package   frapi
 */
class Frapi_Authorization_HTTP_Digest extends Frapi_Authorization implements Frapi_Authorization_Interface
{
    /**
     * The secret key
     *
     * @var string The secret key
     */
    public $secretKey = 'secretKey--&@72';


    /**
     * The digest opaque value
     *
     * @var string The opaque value
     */
    public $opaque = 'opaque';

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
     * The life length of the nonce value.
     *
     * @var integer The nonce life length.
     */
    public $nonceLife = 300;

    /**
     * This variable is used to define whether or not the passwords
     * should be A1 hashed.
     *
     * @var boolean True or False.
     */
    public $passwordsHashed = true;

	/**
	 * This variable contains the parsed digest data
	 */
	protected $digest = null;

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
     * Use a custom secret key.
     *
     * This method is used to modify the secretKey salt value of
     * the Auth object.
     *
     * @param string $secretKey  The new secret key to use when salting
     *                           the authentication challenge.
     * @return void
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * Send the Authentication digest
     *
     * This method is used to send the authentication
     * negotiation and request the authentication headers
     * from the clients.
     *
     * @return void
     */
    public function send()
    {
        header(
            'WWW-Authenticate: Digest ' .
            'realm="' . $this->realm . '", ' .
            'domain="' . $this->baseUrl . '", ' .
            'qop=auth, '.
            'algorithm=MD5, ' .
            'nonce="' . $this->getNonce() . '", ' .
            'opaque="' . $this->getOpaque() . '"'
        );

        header('HTTP/1.1 401 Unauthorized');
        echo 'HTTP Digest Authentication required for "' . $this->realm . '"';
        exit(0);
    }

    /**
     * Get the nonce
     *
     * This method returns the hashed value of a mix of the nonce
     * with the lifetime, the user remote addr and the secret key.
     *
     * @return string A hashed md5 value of the noncelife+remoteaddr+secretKey
     */
    public function getNonce()
    {
        $time = ceil(time() / $this->nonceLife) * $this->nonceLife;
        return hash(
            'md5',
            date('Y-m-d H:i', $time) . ':' .
                $_SERVER['REMOTE_ADDR'] . ':' .
                $this->secretKey
        );
    }

    /**
     * Get the opaque
     *
     * This method returns the opaque value hashed in
     * an md5.
     *
     * @return string $this->opaque hashed in md5.
     */
    public function getOpaque()
    {
        return hash('md5', $this->opaque);
    }

    /**
     * Authorize the request
     *
     * This method is used to authorize the request. It fetches the
     * digest information from the request, decomposes it and finds out
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
        if (!isset($_SERVER['PHP_AUTH_DIGEST'])) {
            return $this->send();
        }

        if ($this->_parseDigest($_SERVER['PHP_AUTH_DIGEST'])) {
            $users = Frapi_Model_Partner::isPartnerHandle($this->digest['username']);

            if ($users === false) {
                return $this->send();
            }

            return $this->_validateResponse($users['api_key']);
        }

        return $this->send();
    }

    protected function _parseDigest($authorization)
    {
        if (preg_match('/username="([^"]+)"/', $authorization, $username) &&
                preg_match('/[,| ]nonce="([^"]+)"/', $authorization, $nonce) &&
                preg_match('/response="([^"]+)"/', $authorization, $response) &&
                preg_match('/opaque="([^"]+)"/', $authorization, $opaque) &&
                preg_match('/uri="([^"]+)"/', $authorization, $uri))
        {
            $this->digest = compact('username', 'nonce', 'response', 'opaque', 'uri');
			$this->digest['username'] = $this->digest['username'][1];
			return true;
        }

        return false;
    }

	protected function _validateResponse($data)
	{
		$requestURI = $_SERVER['REQUEST_URI'];
		$_SERVER['X_FRAPI_AUTH_USER'] = $this->digest['username'];

		if (strpos($requestURI, '?') !== false) {
			$requestURI = substr($requestURI, 0, strlen($this->digest['uri'][1]));
		}

		if ($this->getOpaque() == $this->digest['opaque'][1] && $requestURI == $this->digest['uri'][1] &&
			$this->getNonce() == $this->digest['nonce'][1]) {

			$passphrase = hash('md5', "{$this->digest['username']}:{$this->realm}:{$data}");

			if ($this->passwordsHashed) {
				$a1 = $passphrase;
			} else {
				$a1 = md5($this->digest['username'] . ':' . $this->realm . ':' . $passphrase);
			}

			$a2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $requestURI);

			if (preg_match('/qop="?([^,\s"]+)/', $_SERVER['PHP_AUTH_DIGEST'], $qop) &&
					preg_match('/nc=([^,\s"]+)/', $_SERVER['PHP_AUTH_DIGEST'], $nc) &&
					preg_match('/cnonce="([^"]+)"/', $_SERVER['PHP_AUTH_DIGEST'], $cnonce)) {
				$expectedResponse =
						md5($a1 . ':' . $this->digest['nonce'][1] . ':' . $nc[1] . ':' . $cnonce[1] . ':' . $qop[1] . ':' . $a2);
			} else {
				$expectedResponse = md5($a1 . ':' . $this->digest['nonce'][1] . ':' . $a2);
			}

			if ($this->digest['response'][1] == $expectedResponse) {
				return $this->digest['username'];
			}
		}

		return $this->send();
	}
}
