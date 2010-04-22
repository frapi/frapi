<?php

/**
 * Partner Model
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
class Frapi_Model_Partner
{
    /**
     * This function is called from Frapi_Security::isPartner
     * so this function must actually implement the validation
     * of the partner.
     *
     * @param String $partnerID  Partner identifier (email).
     * @param String $partnerKey Partner key (sha1 hash).
     *
     * @return Boolean Whether the supplied partnerID and partnerKey are valid details.
     */
    public static function isPartner($partnerID, $partnerKey)
    {
        if (!($partners = Frapi_Internal::getCached("Partners.emails-keys"))) {            
            $partners = Frapi_Internal::getCachedPartners();
        }

        if (isset($partners[$partnerID]) && $partners[$partnerID]['email'] == $partnerID &&
            $partners[$partnerID]['api_key'] == $partnerKey) 
        {
            return true;
        }

        return false;
    }
}
