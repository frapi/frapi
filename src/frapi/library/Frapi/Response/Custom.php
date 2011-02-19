<?php
/**
 * Frapi_Response_Custom
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
 * @since     0.2.0
 * @package   frapi
 */
class Frapi_Response_Custom extends Frapi_Response
{
    /**
     * Set the data
     *
     * This method sets the data variable to be used in the output.
     *
     * @param  string $data The data to set in the response.
     * @return void
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
