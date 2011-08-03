<?php
/**
 * The Lupin SMS package
 *
 * This is the package used to send SMS thorough applications.
 * It could have many drivers and you simply use the code under to
 * generate a text message.
 *
 * Currently available drivers:
 * <ul>
 *  <li>Clickatell</li>
 * </ul>
 *
 * Example:
 * <code>
 *   $sms = Lupin_SMS::getInstance('clickatell');
 *
 *   $sms->setParams(array(
 *      'user'     => 'xxx',
 *      'password' => 'yyy',
 *      'api_id'   => 'zzz',
 *   ));
 *
 *   $sms->sendMessage('Does that work?', '353863209369');
 * </code>
 *
 * @package   Lupin
 */
class Lupin_SMS
{
    /**
     * A list of instances that contains all the sms driver instances.
     *
     * @var array
     */
    public static $instances = array();

    /**
     * Get instance
     *
     * This public static function will get you an instance of the driver
     * you requested. If this instance doesn't exist, it'll create a new
     * one and save it to self::$instances to be reused later.
     *
     * @uses   self::$instances
     * @param  string $driverName      The SMS driver to use.
     * @return Lupin_SMS_Abstract  An instance of the SMS driver.
     */
    public static function getInstance($driverName)
    {
        if (isset(self::$instances[$driverName])) {
            return self::$instances[$driverName];
        }

        $driver = __CLASS__ . '_' . ucfirst(strtolower($driverName));
        self::$instances[$driverName] = self::factory($driver);

        return self::$instances[$driverName];
    }

    /**
     * Stupid factory
     *
     * This is a very stupid factory that in facts uses autoload and
     * assumes that the file exists.
     *
     * @param string $className         The class to auto-load.
     * @return Lupin_SMS_Abstract   A new SMS driver instance.
     */
    public static function factory($className)
    {
        return new $className;
    }
}
