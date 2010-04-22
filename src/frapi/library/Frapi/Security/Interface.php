<?php
/**
 * Security
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
 * This interface provides an easy access to the security
 * methods that are to be used in order to identify a user
 * or a partner.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
interface Frapi_Security_Interface
{
    /**
     * Is partner
     *
     * This method checks if the information passed
     * to the method is valid and if it is indeed
     * one of our partners. If so return true.
     *
     * @param  string $partnerID   The partner ID
     * @param  string $partnerKey  The partner Key
     * @return bool   If it is a valid partner or not.
     */
    public function isPartner($partnerID, $partnerKey);
}
