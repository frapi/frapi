<?php
/**
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
 * @package   frapi-admin
 */
class CacheController extends Lupin_Controller_Base
{
    /**
     * Cached keys to be used across the system
     *
     * @var array An array of cached keys
     */
    private $cache_keys = array();
    
    /**
     * The cache key hashes. 
     *
     * @var string A hash of the server remote address.
     */
    private $hash = false;
    
    /** 
     * This variable holds the Frapi_Cache object
     *
     * @var Frapi_Cache The cache object
     */
    private $cache = false;
    
    /**
     * Initialize 
     *
     * This method does the pre-requisite for the Controller to work
     * and function correctly
     *
     * @return void
     */
    public function init($styles = array())
    {
        $actions = array('index', 'detail', 'unset', 'unsetall');
        $this->_helper->_acl->allow('admin', $actions);
        
        $configModel = new Default_Model_Configuration();
        $server = $configModel->getKey('api_url');
        $cache = $configModel->getKey('cache');
        $this->cache = Frapi_Cache::getInstance(FRAPI_CACHE_ADAPTER);
        
        $this->hash = isset($server) ? hash('sha1', $server) : '';

        $this->cache_keys = array(
            "Output.default-format", 
            "Errors.user-defined", 
            "Router.routes-prepared", 
            "Internal.database-dsn",
            "Output.formats-enabled",
            "Actions.enabled-private",
            "Actions.enabled-public",
            "Partners.emails-keys",
            "Cache.adapter",
            'Database.configs'
        );
        
        parent::init($styles);
    }

    /**
     * The index action
     *
     * This is the list of cached keys
     *
     * This method defines a method and recurisvely calls itself
     * 
     * @return void
     */
    public function indexAction() 
    {   
        function deepStrlen($data)
        {
            if (is_scalar($data)) {
                return strlen($data);
            } else if (is_array($data)) {
                $sum = 0;
                foreach ($data as $k=>$v) {
                    $sum += deepStrlen($v);
                }
                return $sum;
            }
            else if (is_object($data)) {
                return 0;
            }
        }    
            
        $profile = array();
        $sets    = array();
        $names   = array();
        
        foreach ($this->cache_keys as $k=>$key) {
            $value = $this->cache->get($this->hash . '-' . $key);
            $isset = $value !== false ? true : false;

            $profile [$key]= array(
                'set'   => $isset,
                'value' => $value, 
                'size'  => ($isset ? deepStrlen($value) :'N/A'),
            );

            $sets [$k] = $isset;
            $names[$k] = $key;
        }
        
        array_multisort($sets, SORT_DESC, $names, SORT_ASC, $profile);
        $this->view->profile = $profile;
    }
    
    /**
     * Cache details
     *
     * This method returns a list of details about a certain 
     * cached action.
     *
     * @return void
     */
    public function detailAction() 
    {
        $deats = $this->cache->get(
            $this->hash . '-' . ($key = $this->getRequest()->getParam('key'))
        );
        
        $this->view->deats = $deats;
        $this->view->key   = $key;
    }
    
    /** 
     * Unset a key
     *
     * This method is used when a cached key gets unset
     *
     * @return void
     */
    public function unsetAction()
    {
        $this->cache->delete(
            $this->hash . '-' . $this->getRequest()->getParam('key')
        );
        
        $this->_redirect('/cache');
    }
    
    /**
     * Remove all cached keys
     *
     * This method removes all cached keys.
     *
     * @return void
     */
    public function unsetallAction()
    {
        foreach ($this->cache_keys as $key) {
            $this->cache->delete($this->hash . '-' . $key);
        }
        
        $this->_redirect('/cache');
    }
}
