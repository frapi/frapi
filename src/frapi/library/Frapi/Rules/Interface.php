<?php
/**
 * Rules interface
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
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
interface Frapi_Rules_Interface
{
    /**
     * Validate the action type
     *
     * This method validates that the action
     * type passed is a valid one.
     *
     * @param  string $type  The type to validate
     * @return bool   Type is valid or is not.
     */
    public static function validateActionType($type);

    /**
     * Validate Output Type
     *
     * This method validates the outputcontext called.
     *
     * It checks if the value passed is in the allowed array
     * of validOutputs.
     *
     * @see    self::$validOutputs
     * @return bool Valid or not.
     */
    public static function validateOutputType($type);
}