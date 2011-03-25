<?php
/**
 * OAuth2 Authorization helper
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
 * This class is mostly used as a helper for people that want to integrate with OAuth2.
 * it provides them with what is essentially and that is retrieving the access token
 * from either the headers or the parameters passed.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
abstract class Frapi_Authorization_Oauth2 extends Frapi_Authorization
    implements Frapi_Authorization_Interface
{
    /**
     * Get the access token.
     *
     * According to the OAuth2 Specs, the access token can come in
     * two official formats. Either in the request or in the headers.
     *
     * This method is a helper than enables the OAuth2 providers
     * to retrieve the access_token passed to the web-service either
     * in the request parameters or in the Header with the format
     *   Authorization: OAuth XXX
     *
     * @throws Frapi_Authorization_Oauth2_Exception
     *
     * @return string The access token retrieved.
     */
    public function getAccessToken()
    {
        // Gotta love moving specs, prior to rev-10 of
        // the oauth2 specs, this param was still oauth_token
        if (isset($this->params['access_token']) &&
            strlen(trim($this->params['access_token'])) > 0)
        {
            return $this->params['access_token'];
        }

        // If we didn't find it in there, it may be in the
        // $_SERVER array then. Let's look for and Authorization
        // header value that contains "OAuth".

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['HTTP_AUTHORIZATION'];
            if (stristr('oauth', $header) !== 0) {
                list($null, $token) = explode(' ', $header);

                if (isset($token) && strlen(trim($token)) > 0) {
                    return $token;
                }
            }
        }

        // We couldn't find it in the $_SERVER so let's try
        // to see if we are using apache and let's use getallheaders.
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $header = $headers['Authorization']
            if (stristr('oauth', $header) !== 0) {
                list($null, $token) = explode(' ', $header);

                if (isset($token) && strlen(trim($token)) > 0) {
                    return $token;
                }
            }
        }
        // Here we could throw an error with Frapi_Error
        // but OAuth2 requires to follow a certain standard
        // for error messages. Here we return a Frapi_Authorization_Oauth2_Exception
        // with the data that matches the specs.
        throw new Frapi_Authorization_Oauth2_Exception(
            'invalid_client',
            'No valid token was found in the request',
            401
        );
    }
}
