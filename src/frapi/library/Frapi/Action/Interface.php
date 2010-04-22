<?php
/**
 * Action Interface
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
 * Simply the interface for any actionType actions.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
interface Frapi_Action_Interface
{
    /**
     * To Array
     *
     * This method returns the value found in the database
     * into an associative array.
     *
     * @return array  An array of the data received.
     */
    public function toArray();

    // Execute the action and return the value
    public function executeAction();
}