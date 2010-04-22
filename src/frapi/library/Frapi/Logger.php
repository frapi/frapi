<?php
/**
 * Frapi Logger Class
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
class Frapi_Logger
{
    /**
     * Date format
     *
     * @var String
     **/
    const DATE_FORMAT = "Y-m-d\Tg:i:s";

    /**
     * Main log filename
     *
     * @var String
     **/
    const MAIN_FILENAME = "/var/log/frapi/main.log";

    /**
     * Error log filename
     *
     * @var String
     **/
    const ERROR_FILENAME = "/var/log/frapi/error.log";

    /**
     * General logging method
     *
     * @return void
     **/
    private function log($file, $text) {
        try {
            $handle = fopen($file, "ab");
            fwrite($handle, date(Frapi_Logger::DATE_FORMAT) . " " . $text . "\n");
            fclose($handle);
        } catch (Exception $e) {
            // Silently ignore logging errors
        }
    }

    /**
     * Main logging static method
     *
     * @return void
     **/
    static function main($text) {
        Frapi_Logger::log(Frapi_Logger::MAIN_FILENAME, $text);
    }

    /**
     * Error logging static method
     *
     * @return void
     **/
    static function error($text) {
        Frapi_Logger::log(Frapi_Logger::ERROR_FILENAME, $text);
    }
}
