<?php
/**
 * Configuration Helper
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
 * @package   frapi
 */
 
 
class Frapi_ParamHelper
{
  

    /**
     * Retrieve the cached configuration parameter
     *
     * This method retrieves the cached parameter. If the caching method
     * does not identify anything from the cache then we parse the XML file.
     *
     * @param string $key The key of the cached parameter to fetch.
     * @return string The parameter value.
     */
    public static function getParam($key)
    {
        if ($cached = Frapi_Internal::getCached('Params.'.$key)) {
            return $cached;
        } else {

            $conf  = Frapi_Internal::getConfiguration('params');
            $params = $conf->getAll('param');

            if ($params !== false) {
                foreach ($params as $param) {
                    if($key == $param['key'])
            			Frapi_Internal::setCached('Params.'.$key, $param['value']);       	
                }
            }
            return Frapi_Internal::getCached('Params.'.$key);
        }
    }
}