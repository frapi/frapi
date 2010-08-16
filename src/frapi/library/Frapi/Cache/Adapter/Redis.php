<?php
/**
 * Frapi Redis Cache Adapter Layer
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
 * The Redis adapter of the Frapi Cache. In order to run this, you will need
 * to install phpredis from: http://github.com/owlient/phpredis
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Cache_Adapter_Redis implements Frapi_Cache_Interface 
{
    /** 
     * The redis object.
     *
     * @var Redis $redis The redis object.
     */
    private $redis = null;
    
    /** 
     * Constructor
     *
     * This is the constructor. The options passed can be the
     * ip of the server, or an array of servers and their port.
     *
     * Example:
     * <code>
     *
     *     $options = array(
     *         'hostname' => '127.0.0.1',
     *         'port'     => 6666
     *     ); 
     *
     *     $cache   = Frapi_Cache_Adapter_Redis($options);
     * </code>
     *
     * @param array $options an array of options
     * @return void
     */
    public function __construct(array $options)
    {
        $this->redis = new Redis();

        $defaults = array('hostname' => '127.0.0.1', 'port' => 6379);
        $options += $defaults;

        $this->redis->connect($options['hostname'], $options['port']);
    }
    
    /**
     * Get a cache variable
     *
     * Retrieve a variable from the cache and return it's
     * value to the user.
     *
     * @param  string $name  The name of the cache variable to retrieve.
     *
     * @return mixed         The value of the retrieved variable or false if
     *                       variable isn't found.
     */
    public function get($name) 
    {
        $key = $this->redis->get($name);

        if ($key !== false) {
            return unserialize($key);
        }
        
        return false;
    }

    /**
     * Add to the cache
     *
     * Add a new variable to the cache that you will then be able
     * to retrieve using the $this->get($name) method.
     *
     * @param  string  $name   The name of the cache variable to store.
     * @param  string  $value  The value of the cache variable to store.
     * @param  integer $expire When should it expire? Default: 900 seconds.
     * 
     * @return boolean       Depending on the success of the operation, 
     *                       either true or false. 
     */
    public function add($name, $value, $expiry = 900) 
    {
        $this->redis->add($name, serialize($value));
        $this->redis->setTimeout($name, $expiry);
    }
    
    /**
     * Delete from the cache
     *
     * Delete a variable from the cache so it is no longer usuable and
     * retrievable by $this->get($name)
     *
     * @param  string $name  The name of the cache variable to delete.
     * 
     * @return boolean       Depending on the success of the operation, 
     *                       either true or false. 
     */
    public function delete($name) 
    {
        return $this->redis->delete($name);
    }
}
