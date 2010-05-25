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
 * @copyright echolibre ltd.
 * @package   frapi-admin
 */
class CacheController extends Lupin_Controller_Base
{
    private $cache_keys = array();
    private $hash = false;
    
    public function init()
    {
        $actions = array('index', 'detail', 'unset', 'unsetall');
        $this->_helper->_acl->allow('admin', $actions);
        
        $configModel = new Default_Model_Configuration();
        $server = $configModel->getKey('api_url');
        $cache = $configModel->getKey('cache');
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
        );
        
        parent::init();
    }

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
            
            $profile [$key]= array(
                'set'   => ($isset = ($value = apc_fetch($this->hash . '-'. $key) !== false ? true : false)),
                'value' => $value, 
                'size'  => ($isset ? deepStrlen($value) :'N/A'),
            );
            
            $sets [$k] = $isset;
            $names[$k] = $key;
        }
        
        array_multisort($sets, SORT_DESC, $names, SORT_ASC, $profile );
        $this->view->profile = $profile;
    }
    
    public function detailAction() 
    {
        $deats = apc_fetch($this->hash . '-' . ($key = $this->getRequest()->getParam('key')));
        $this->view->deats = $deats;
        $this->view->key   = $key;
    }
    
    public function unsetAction()
    {
        apc_delete($this->hash . '-' . $this->getRequest()->getParam('key'));
        $this->_redirect('/cache');
    }
    
    public function unsetallAction()
    {
        foreach ($this->cache_keys as $key) {
            apc_delete($this->hash . '-' . $key);
        }
        
        $this->_redirect('/cache');
    }
}