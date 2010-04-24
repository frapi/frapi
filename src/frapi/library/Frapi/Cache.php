<?php
class Frapi_Cache_Exception extends Frapi_Exception {}

/**
 * Frapi Cache Layer
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
 * This class is specifically so we can easily move from a cache adapter
 * to another without too much hassle. Right now frapi is very APC dependent
 * and this can't be good as APIs will have issues running windows.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Cache
{
    public $adapter = 'apc';
    public $cacheObject = null;
    
    public function __construct($adapter = 'apc')
    {
        $this->cacheObject = Frapi_Cache::getInstance($adapter);
    }
    
    public static function getInstance($adapter = 'apc')
    {
        $adapter     = ucfirst(strtolower($adapter));
        $adapterFile = LIBRARY_CACHE_ADAPTER . DIRECTORY_SEPARATOR . $adapter . '.php';
        $className   = 'Frapi_Cache_Adapter_' . $adapter;
        
        if (!class_exists($className)) {
            require_once $adapterFile;
            return new $className;
        }
                     
        return new $className;
    }
}
