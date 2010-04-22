<?php
/**
 * Authorization Interface
 *
 * The interface with the methods that
 * each Authorization must implement.
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
interface Frapi_Authorization_Interface
{
    /**
     * Authorize
     *
     * This method will verify the data that has been
     * passed and authorize it or not.
     *
     * It will return an error in case it does
     * not authorize.
     *
     * @return  mixed   Error in case it is not valid
     *                  True if it is for partner, or if login
     *                  is valid.
     */
    public function authorize();
}