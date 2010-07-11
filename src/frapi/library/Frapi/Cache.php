<?php
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
    /**
     * The adapter name to load
     *
     * @var string $adapter The name of the adapter driver.
     */
    public $adapter;
    
    /**
     * The cache object that holds the cache connector
     *
     * @var Frapi_Cache $cacheObject The cache object.
     */
    public $cacheObject = null;
    
    /**
     * Constructor
     *
     * This is the constructor of the Frapi_Cache class.
     *
     * @param  string $adapter The adapter to load.
     * @param  array  $options Potentially a list of options per adapter.
     * @return void
     */
    public function __construct($adapter = 'apc', $options = array())
    {
        $this->cacheObject = Frapi_Cache::getInstance($adapter);
    }
    
    /**
     * Create an instance of the cache
     *
     * This method accepts a paramter and will return a cache object
     * of connecting to the adapter you specified.
     *
     * @param  string $adapter The adapter to load.
     * @param  array  $options Potentially a list of options per adapter.
     * @return Frapi_Cache_Interface A new class of the requested adapter type.
     */
    public static function getInstance($adapter = 'apc', $options = array())
    {
        $adapter     = ucfirst(strtolower($adapter));
        $adapterFile = LIBRARY_CACHE_ADAPTER . DIRECTORY_SEPARATOR . $adapter . '.php';
        $className   = 'Frapi_Cache_Adapter_' . $adapter;
        
        if (!file_exists($adapterFile)) {
            throw new Frapi_Cache_Adapter_Exception(
                "$className does not exist.", 'Frapi_Cache_Adapter_Exception'
            );
        }
        
        if (!class_exists($className, false)) {
            require_once $adapterFile;
        }
                     
        return new $className($options);
    }
}
