<?php
class Frapi_Rules_Exception extends Frapi_Exception {}

/**
 * Rules class
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
 * This class contains the rules about actions/outputs
 *
 * It mostly contains methods that are there to validate
 * the types and actions requested.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Rules implements Frapi_Rules_Interface
{
    /**
     * This method validates that the action type passed is a valid one.
     *
     * This method will look in the loginrequired and partnerId (pid) required
     * actions array for the key passed. If it's missing, it's not allowed.
     *
     * ANY ACTION must be either logged or contain partner id/key
     *
     * @param  string $type  The type to validate
     * @return bool   Type is valid or it is not.
     */
    public static function validateActionType($type)
    {
        if (!Frapi_Rules::isPartnerAction($type)) {
            return false;
        }

        return true;
    }

    /**
     * This method validates the outputcontext called.
     *
     * It checks if the value passed is in the allowed array
     * of validOutputs.
     *
     * @see    self::$validOutputs
     * @return bool Valid or not.
     */
    public static function validateOutputType($type)
    {
        $outputs = Frapi_Internal::getCachedElseQueryConfigurationByKey(
            'Output.formats-enabled',
            array(
                'type' => 'outputs', 
                'node' => 'output', 
                'key'  => 'name'
            )
        );

        if (is_array($outputs) && !in_array(strtolower($type), $outputs)) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if it is a partner action or not.
     *
     * @param  string  $action The action
     * @return bool    If it's a valid action or not.
     */
    public static function isPartnerAction($action)
    {
        $actions = Frapi_Internal::getCachedActions('private');

        if (is_array($actions) && in_array(strtolower($action), $actions)) {
            return true;
        }

        return false;
    }

    /**
     * Check if it is a public action or not.
     *
     * @param  string  $action The action
     * @return bool    If it's a valid action or not.
     */
    public static function isPublicAction($action)
    {
        $actions = Frapi_Internal::getCachedActions('public');

        if (is_array($actions) && !in_array($action, $actions)) {
            return false;
        }

        return true;
    }
}
