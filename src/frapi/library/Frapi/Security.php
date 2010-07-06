<?php
class Frapi_Security_Exception extends Frapi_Exception {}

/**
 * Security
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
 * This context is the one that is going to be executing
 * the security checks to validate the user and partner
 * information thorough the webservice.
 *
 * The security context verifies if the actions are performed
 * by either someone that is a **partner**. In the case of public
 * actions, no verification is needed here.
 *
 * @uses    Frapi_Security_Interface
 * @uses    Frapi_Security_Exception
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Security implements Frapi_Security_Interface
{
    /**
     * This method checks if the information passed
     * to the method is valid and if it is indeed
     * one of our partners. If so return true.
     *
     * @deprecated
     *
     * @param  string $partnerID   The partner ID
     * @param  string $partnerKey  The partner Key
     * @return bool   If it is a valid partner or not.
     */
    public function isPartner($partnerID, $partnerKey)
    {
        // IP Validation should happen here.
        $model = Frapi_Model_Partner::isPartner($partnerID, $partnerKey);
        header('WWW-Authenticate: Basic realm="API Authentication"');
        
        if ($model === false) {
            throw new Frapi_Error(
                Frapi_Error::ERROR_INVALID_PARTNER_ID_NAME,
                Frapi_Error::ERROR_INVALID_PARTNER_ID_MSG,
                Frapi_Error::ERROR_INVALID_PARTNER_ID_NO
            );
        }

        return true;
    }
}
