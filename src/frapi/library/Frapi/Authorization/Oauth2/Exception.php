<?php
/**
 * OAuth2 Authorization helper Exception
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
 * @package   frapi
 */
class Frapi_Authorization_Oauth2_Exception extends Frapi_Exception
{
    /**
     * Get the error array
     *
     * We need to override the error because OAuth2 has a different
     * error format than we do.
     *
     * @return array an array of the error returned by the OAuth2 Helper.
     */
    public function getErrorArray()
    {
        return array(
            'error' => $this->getMessage(),
            'error_description' => $this->getName()
        );
    }
}
