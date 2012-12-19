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
class Default_Model_Param extends Lupin_Model
{
    protected $config;
    
    public function __construct()
    {
        $this->config = new Lupin_Config_Xml('params');
    }
    
    public function add(array $data)
    {
        

        $values = array(
            'key' => $data['key'], 
            'value'  => $data['value'],
        );

        try {
            $res = $this->config->add('param', $values);
        } catch (Exception $e) { 

        }
        
        $this->refreshAPCCache();
        return true;
    }

    public function edit(array $data, $id)
    {
        $values = array(
            'key' => $data['key'], 
            'value'  => $data['value'],
        );

        try {
            $this->config->update('param', 'hash', $id, $values);
        } catch (Exception $e) { }
        
        $this->refreshAPCCache($data['key']);
        return true;
    }

    
    public function delete($id)
    {
        $this->config->deleteByField('param', 'hash', $id);
        $this->refreshAPCCache($data['key']);
    }

    public function get($id)
    {
        $param = $this->config->getByField('param', 'hash', $id);
        return isset($param) ? $param : false;
    }
    
    public function getByKey($key)
    {
        $param = $this->config->getByField('param', 'key', $key);
        return isset($param) ? $param : false;
    }


    
    public function getAll()
    {
        $params = $this->config->getAll('param');
        return $params;
    }
    
    /**
     * Refresh the APC cache by deleting APC entries.
     *
     * @return void
     **/
    public function refreshAPCCache($key = null)
    {
        $configModel = new Default_Model_Configuration();
        $server = $configModel->getKey('api_url');
        $hash = isset($server) ? hash('sha1', $server) : '';
        
        $cache = Frapi_Cache::getInstance(FRAPI_CACHE_ADAPTER);
        if($key!=null)
        	$cache->delete($hash . '-Params.'.$key);
        $cache->delete($hash . '-configFile-params');
    }
}
