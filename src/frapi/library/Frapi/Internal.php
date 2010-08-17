<?php
class Frapi_Internal_Exception extends Frapi_Exception {}

/**
 * Frapi Internal Db Class
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
 * This class is specifically for getting
 * various pieces of information about the
 * API from the admin database. Actions, errors
 * and output types must be validated, but initializing
 * and querying the back-end SQLite (or other) database is SLOW.
 * Therefore, Memcache or APC caching is used, and a SINGLE static
 * instance of the db handle to the SQLite db is maintained!
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Internal
{
    /**
     * Database Handle
     *
     * Keeps a static handle for the admin database.
     *
     * @var PDO Database Adapter
     */
    protected static $dbHandle = null;
    
    /**
     * This holds the Frapi_Cache object
     *
     * @param Frapi_Cache $cache The cache object.
     */
    protected static $cache;
    
    protected static $conf = array();

    /**
     * Log variable for debugging
     *
     * @var Array
     */
    protected static $_log = null;

    /**
     * This is the hash of your server's hostname. It may seem as a bug
     * or an inflexible solution and may be changed at a later point, however
     * frapi runs on it's own hostname by design and is not sharing domain
     * names just yet.
     *
     * The reason for the hash was that on the same server with multiple vhosts
     * there would be cache collisions and conflicts thus the need for a special
     * hash in the apc keys.
     */
    private static $_hash = false;
    
    /**
     * Logging function
     *
     * @return void
     */
    protected static function log($type, $extra = null)
    {
        if (Frapi_Controller_Main::MAIN_WEBSERVICE_DEBUG) {
            if (is_null(self::$_log)) {
                self::$_log = array(
                    'cache-get'    => array('times'=>0, 'keys'=>array()),
                    'cache-set'    => array('times'=>0, 'keys'=>array()),
                    'cache-delete' => array('times'=>0, 'keys'=>array()),
                    'db'           => array('times'=>0)
                );
            }

            switch ($type)
            {
                case 'cache-get':
                case 'cache-set':
                case 'cache-delete':
                    self::$_log[$type]['times']++;
                    self::$_log[$type]['keys'] []= $extra;
                    break;
                case 'db':
                    self::$_log[$type]['times']++;
                    break;
            }
        }
    }

    /**
     * Get the log
     *
     * @return Array | null
     */
    public function getLog() 
    {
        return self::$_log;
    }
    
    /**
     * Get the configuration
     *
     * This method retrieves and loads the information.
     *
     * @TODO This method has to be optimized -- The fetched object
     * has to be cached so we don't parse XML files each time. If we cache the 
     * $xml object directly into APC we end up with Incomplete classes, and if we 
     * encode it using json_encode well we loose all it's properties (access to methods).
     *
     * @param  string $type  The configuration to load.
     * @return array The configuration for a type
     */
    public static function getConfiguration($type)
    {
        if (!isset(self::$cache)) {
            self::$cache = Frapi_Cache::getInstance(FRAPI_CACHE_ADAPTER);   
        }
        
        if (!isset(self::$conf[$type])) {
            if (1 == 2 && $cachedConfig = self::getCached('Internal.configuration.type.' . $type)) {
                return $cachedConfig;
            } else {
                $xml = new Lupin_Config_Xml($type);
                self::$conf[$type] = $xml;
                //self::setCached('Internal.configuration.type.' . $type, self::$conf[$type]);
            }
        }
        
        return self::$conf[$type];
    }

    /**
     * Get `key` from cache, if not found check in configuration file
     *
     * As above, but returns single column.
     *
     * @return Mixed
     */
    public static function getCachedElseQueryConfigurationByKey($key, array $query)
    {
        if ($cached = self::getCached($key)) {
            return $cached;
        } else {
            
            $res  = self::getConfiguration($query['type']);
            $rows = $res->getAll($query['node']);

            $return = array();
            if ($rows !== false) {
                foreach ($rows as $row) {
                    $return[] = strtolower($row[$query['key']]);
                }

                if (!self::setCached($key, $return)) {
                    return $return;
                }
            }
        
            return $return;
        }
    }
    
    /**
     * Retrieve the cached partners
     *
     * This method retrieves the cached partners. If the caching method
     * does not identify anything from the cache then we parse the XML file.
     *
     * @param string $type The type of cached partners to fetch.
     * @return array A list of partners retrieved that have valid information.
     */
    public static function getCachedPartners($type = 'keys')
    {
        if ($cached = self::getCached('Partners.emails-' . $type)) {
            return $cached;
        } else {
        
            $res  = self::getConfiguration('partners');
            $rows = $res->getAll('partner');
        
            if ($rows !== false) {
                foreach ($rows as $key => $value) {
                    $users[$value['email']] = $value;
                }
                
                if (!self::setCached('Partners.emails-' . $type,  $users)) {
                    return $users;
                }
            }
    
            return self::getCached('Partners.emails-' . $type);
        }
    }

    /**
     * Retrieve the cached db configuration
     *
     * This method retrieves the cached configuration. If the caching method
     * does not identify anything from the cache then we parse the XML file.
     *
     * @param string $type The type of cached database configs to fetch.
     * @return array A list of db configs retrieved that have valid information.
     */
    public static function getCachedDbConfig()
    {
        if ($cached = self::getCached('Database.configs')) {
            return $cached;
        } else {
        
            $res  = self::getConfiguration('configurations');
            $rows = $res->getAll('configuration');
            
            if ($rows !== false) {
                foreach ($rows as $key => $value) {
                    $confs[$value['key']] = $value['value'];
                }
                
                if (!self::setCached('Database.configs',  $confs)) {
                    return $confs;
                }
            }
    
            return self::getCached('Database.configs');
        }
    }

    /**
     * Get the cached actions
     *
     * Retrieve a list of cached actions. This method will also retrieve
     * and cache a list of private and public actions.
     *
     * The private actions are the actions taht require a partner username/password
     * in order to be able to access them.
     *
     * @param  string $type Retrieve the type of partner (public or private)
     * @return array A list of partners.
     */
    public static function getCachedActions($type = 'public')
    {
        if ($cached = self::getCached('Actions.enabled-' . $type)) {
            return $cached;
        } else {
            
            $res  = self::getConfiguration('actions');
            $rows = $res->getAll('action');

            $private = array();
            $public  = array();
            
            if ($rows !== false) {
                foreach ($rows as $row) {
                    if (!is_array($row) || empty($row)) {
                        continue;
                    }

                    if ($row['public'] == '1' && $row['enabled'] == '1') {
                        $public[strtolower($row['name'])] = strtolower($row['name']);
                    } elseif ($row['public'] == '0' && $row['enabled'] == '1') {
                        $private[strtolower($row['name'])] = strtolower($row['name']);
                    }
                }

                if (!self::setCached('Actions.enabled-public',  $public)) {
                    return $public;
                }
                
                if (!self::setCached('Actions.enabled-private', $private)) {
                    return $private;
                }
            }
        
            return self::getCached('Actions.enabled-' . $type);
        }
    }

    /**
     * Get a hash of your server
     * 
     * If you happen to have multiple installations of frapi
     * you would get apc cache collisions if we woulnd't have
     * some sort of hashing and identification of the hostnames.
     *
     * Right now this hash is very rudimentary, it's simply and sha1
     * hash of the HTTP_HOST that you are serving frapi from.
     *
     * @return string self::$_hash The sha1-server hash
     */
    public static function getHash()
    {
        if (self::$_hash) {
            return self::$_hash;
        }
        
        self::$_hash = hash('sha1', $_SERVER['HTTP_HOST']);
        return self::$_hash;
    }
    
    /**
     * Get a (possibly cached) value from a number of caches.
     *
     * For instance, memcache may be checked, then APC etc..
     * False on failure to find value.
     *
     * @param  string $key Key name
     * @return mixed  Either a boolean false or the value of the cached value.
     */
    public static function getCached($key)
    {
        self::log('cache-get', $key);
        $hash = self::getHash();

        if (!isset(self::$cache)) {
            self::$cache = Frapi_Cache::getInstance(FRAPI_CACHE_ADAPTER);
        }

        if (($cacheVal = self::$cache->get($hash . '-' . $key)) !== false) {
            return $cacheVal;
        }

        return false;
    }

    /**
     * Cache a key-value
     *
     * Store a cached $key=>$value pair in a series
     * of caches. E.g., memcache and APC.
     *
     * @param  string $key   The key to store the value under.
     * @param  mixed  $value The value to cache.
     * @return void
     */
    public static function setCached($key, $value)
    {
        self::log('cache-set', $key);
        $hash = self::getHash();
        
        if (!isset(self::$cache)) {
            self::$cache = Frapi_Cache::getInstance(FRAPI_CACHE_ADAPTER);
        }
        
        return self::$cache->add($hash . '-' . $key, $value);
    }

    /**
     * Delete key from cache(s).
     *
     * This method deletes a key from the cache instance.
     *
     * @param  string  The cached key to delete.
     * @return void
     */
    public static function deleteCached($key)
    {
        self::log('cache-delete', $key);
        $hash = self::getHash();
        
        if (!isset(self::$cache)) {
            self::$cache = Frapi_Cache::getInstance(FRAPI_CACHE_ADAPTER);
        }
        
        return self::$cache->delete($hash.'-'.$key);
    }
}
