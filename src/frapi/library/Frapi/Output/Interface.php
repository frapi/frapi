<?php
/**
 * OuputInterface
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
 * Simply the interface to every outputType
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
interface Frapi_Output_Interface
{
    /**
     * Populate the Output
     *
     * This method populates the $this->response
     * variable with the value returned from the
     * action.
     *
     * @param Mixed $response Most of the times an array but could be
     *                        an stdClass
     * @param String $customTemplate The custom template file to use instead of the default one.
     */
    public function populateOutput($response, $customTemplate = false);

    /**
     * Execute the output
     *
     * This method will basically return the value
     * of $this->response with the desired type.
     *
     * @return string
     */
    public function executeOutput();
}