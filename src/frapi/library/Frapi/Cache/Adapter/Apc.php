<?php
/**
 * Frapi APC Cache Adapter Layer
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
 * The APC adapter of the Frapi Cache.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Cache_Adapter_Apc implements Frapi_Cache_Interface 
{
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
        return apc_fetch($name);
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
        return apc_store($name, $value, $expiry);
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
        return apc_delete($name);
    }
}