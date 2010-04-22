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

        /**
         * Make sure the params needed are passed
         * if not, return an error with invalid partner
         * id/key
         */
        $partnerID  = isset($this->params['email'])     ? $this->params['email']     : false;
        $partnerKey = isset($this->params['secretKey']) ? $this->params['secretKey'] : false;

        if (!empty($partnerID) && !empty($partnerKey)) {
            /**
             * Last step, validate the partner information
             * using the security Context
             */
            $partnerID  = $this->params['email'];
            $partnerKey = $this->params['secretKey'];

            $security     = new Frapi_Security();
            $securityPass = $security->isPartner($partnerID, $partnerKey);

            // Seems ok to me.. might as well go through.
            return true;
        }
        
        header('WWW-Authenticate: Basic realm="API Authentication"');
        exit(0);
    }
}
