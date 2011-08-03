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
class Default_Model_Partner extends Lupin_Model
{
    protected $config;
    
    public function __construct()
    {
        $this->config = new Lupin_Config_Xml('partners');
    }
    
    public function add(array $data)
    {
        // Might put back the while to insure the singularity of this key but
        // for now I'll trust odds that I can't control... hrm.
        $apiKey = $this->generateAPIKey();

        $whitelist = array('firstname', 'lastname', 'email', 'company');
        $this->whiteList($whitelist, $data);


        $values = array(
            'firstname' => $data['firstname'], 
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],     
            'company'   => $data['company'],
            'api_key'    => $apiKey
        );

        try {
            $res = $this->config->add('partner', $values);
        } catch (Exception $e) { 

        }
        
        $this->refreshAPCCache();
        return true;
    }

    public function edit(array $data, $id)
    {
        $whitelist = array('firstname', 'lastname', 'email', 'company');
        $this->whiteList($whitelist, $data);

        $values = array(
            'email'     => $data['email'],   
            'company'   => $data['company'],  
            'lastname'  => $data['lastname'],
            'firstname' => $data['firstname'], 
        );

        try {
            $this->config->update('partner', 'hash', $id, $values);
        } catch (Exception $e) { }
        
        $this->refreshAPCCache();
        return true;
    }

    public function updateAPIKey($id)
    {
        $apiKey = $this->generateAPIKey();

        try {
            $this->config->update('partner', 'hash', $id, array('api_key' => $apiKey));
        } catch (Exception $e) { }
        $this->refreshAPCCache();
    }

    public function delete($id)
    {
        $this->config->deleteByField('partner', 'hash', $id);
        $this->refreshAPCCache();
    }

    public function get($id)
    {
        $partner = $this->config->getByField('partner', 'hash', $id);
        return isset($partner) ? $partner : false;
    }

    public function generateAPIKey()
    {
        return hash('sha1', uniqid(mt_rand(1, 1000000000), true));
    }

    public function getAll()
    {
        $partners = $this->config->getAll('partner');
        return $partners;
    }
    
    /**
     * Refresh the APC cache by deleting APC entries.
     *
     * @return void
     **/
    public function refreshAPCCache()
    {
        $configModel = new Default_Model_Configuration();
        $server = $configModel->getKey('api_url');
        $hash = isset($server) ? hash('sha1', $server) : '';
        
        $cache = Frapi_Cache::getInstance(FRAPI_CACHE_ADAPTER);

        $cache->delete($hash . '-Partners.emails-keys');
        $cache->delete($hash . '-configFile-partners');
    }
}
