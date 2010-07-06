<?php
/**
 * Authorization Partner
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
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Authorization_Partner extends Frapi_Authorization implements Frapi_Authorization_Interface
{
    /**
     * This method will verify the data that has been passed and authorize it or not.
     *
     * It will return an error in case it does not authorize.
     *
     * @return mixed Error in case it is not valid True if it is for
     *               partner, or if login is valid.
     */
    public function authorize()
    {
        $valid = Frapi_Rules::isPartnerAction($this->getAction());
        if (!$valid) {
            return false;
        }

        $auth = new Frapi_Authorization_HTTP_Digest();
        
        /**
         * Make sure the params needed are passed
         * if not, return an error with invalid partner
         * id/key
         */
        if (!empty($this->params['digest'])) {
            $authed = $auth->authorize();
            return true;
        }

        $auth->send();
    }
}
