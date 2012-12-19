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
class Default_Model_Database extends Lupin_Model
{
    protected $config;
    
    public function __construct()
    {
        $this->config = new Lupin_Config_Xml('databases');
    }
    
    public function add(array $data)
    {

        $values = array(
            'hostname'    => $data['hostname'], 
            'dbname'      => $data['dbname'],
            'username'    => $data['username'],     
            'password'    => $data['password'],
            'port'        => $data['port'],
            'engine'      => $data['engine'],
        );

        try {
            $res = $this->config->add('database', $values);
        } catch (Exception $e) { 

        }
        
        $this->refreshAPCCache();
        return true;
    }

    public function edit(array $data, $id)
    {
        $values = array(
            'hostname'    => $data['hostname'], 
            'dbname'      => $data['dbname'],
            'username'    => $data['username'],     
            'password'    => $data['password'],
            'port'        => $data['port'],
            'engine'      => $data['engine'],
        );
        try {
            $this->config->update('database', 'hash', $id, $values);
        } catch (Exception $e) { }
        
        $this->refreshAPCCache();
        return true;
    }

    
    public function delete($id)
    {
        $this->config->deleteByField('database', 'hash', $id);
        $this->refreshAPCCache();
    }

    public function get($id)
    {
        $database = $this->config->getByField('database', 'hash', $id);
        return isset($database) ? $database : false;
    }

    
    public function getAll()
    {
        $databases = $this->config->getAll('database');
        return $databases;
    }
    
    /**
     * Refresh the APC cache by deleting APC entries.
     *
     * @return void
     **/
    public function refreshAPCCache($dbname = null)
    {
        $configModel = new Default_Model_Configuration();
        $server = $configModel->getKey('api_url');
        $hash = isset($server) ? hash('sha1', $server) : '';
        
        $cache = Frapi_Cache::getInstance(FRAPI_CACHE_ADAPTER);
        if($dbname!=null)
        	$cache->delete($hash . '-Databases.'.$dbname);
        $cache->delete($hash . '-configFile-databases');
    }
}
