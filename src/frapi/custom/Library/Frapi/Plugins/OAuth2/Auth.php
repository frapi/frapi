<?php
/**
 * Frapi OAuth2 helper
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
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */

class Frapi_Plugins_OAuth2_Auth extends Frapi_Authorization_Oauth2
{

    /**
     * Constructor
     *
     * Here we construct our object. Basically we build the params
     * that we need to use using the full list of parameters from
     * the Action/Controller file.
     *
     * @param array $params An array of user-parameters.
     */
    public function __construct(array $params = array())
    {
        $this->params = $params;
    }

    /**
     * Authorize the request.
     *
     * This method is currently a placeholder to what will handle the
     * likes of in-memory accessToken retrieval.
     *
     * The $this->getAccessToken() method returns either the string of
     * the token or a Frapi_Response containing the error about the missing
     * access_token passed in the request.
     *
     * The reason we use Frapi_Response in this case is because OAuth2 has a
     * predefined standard for the format of errors and with this Response, the
     * format is followed when json is invoked.
     *
     * @return mixed Either a Frapi_Response containing the error or a string
     *               containing the access_token.
     */
    public function authorize()
    {
        $this->setAuthorizationParams($this->params + $_SERVER);
        $accessToken = $this->getAccessToken();

        if (is_string($accessToken)) {
            // You can use the access_token and retrieve the
            // user info from the database here and return whatever
            // the user-id or an array of information. If no access token
            // is found, we return a Frapi_Response formatted as per
            // the OAuth2 specification.
            return true;

            // Moreover, if the accessToken is expired, you could return
            // Something like:
            //
            // return new Frapi_Response(array(
            //     'code' => 400,
            //     'data' => array(
            //         'error' => 'invalid_grant',
            //         'error_description' => 'The access token is expired.'
            //     )
            // ));
        }

        return $accessToken;
    }
}
