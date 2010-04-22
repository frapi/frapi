<?php

class Default_Model_Configuration extends Lupin_Model
{
    protected $config;
    
    public function __construct()
    {
        $this->config = new Lupin_Config_Xml('configurations');
    }

    public function getDbConfig() 
    {
        $return = array();
        
        $hostname = $this->config->getByField('configuration', 'key', 'db_hostname');
        $database = $this->config->getByField('configuration', 'key', 'db_database');
        $username = $this->config->getByField('configuration', 'key', 'db_username');
        $password = $this->config->getByField('configuration', 'key', 'db_password');
        
        if (isset($hostname) && isset($hostname['value'])) {
            $return[] = $hostname;
        }
        
        if (isset($database) && isset($database['value'])) {
            $return[] = $database;
        }
        
        if (isset($username) && isset($username['value'])) {
            $return[] = $username;
        }
        
        if (isset($password) && isset($password['value'])) {
            $return[] = $password;
        }
        
        return $return;
    }
    
    public function addDb(array $data)
    {        
        try {
            $this->config->add('configuration', array(
                'key'   => 'db_hostname', 
                'value' => $data['db_hostname'],
            ));
            
            $this->config->add('configuration', array(
                'key'   => 'db_database', 
                'value' => $data['db_database'],
            ));
        
            $this->config->add('configuration', array(
                'key'   => 'db_username', 
                'value' => $data['db_username'],
            ));
            
            $this->config->add('configuration', array(
                'key'   => 'db_password', 
                'value' => $data['db_password'],
            ));
        } catch (Exception $e) {}
        
        return true;
    }
    
    public function updateByKey($key, $value)
    {
        $res = $this->config->getByField('configuration', 'key', $key);
        
        if ($res === false) {
            return $this->config->add('configuration', array(
                'key'  => $key,
                'value' => $value,
            ));
        }

        return $this->config->update(
            'configuration', 'key', $key, array('value' => $value));
    }
    
    public function editDb(array $data)
    {
        $hostname = $data['db_hostname'];
        $username = $data['db_username'];
        $password = $data['db_password'];
        $database = $data['db_database'];
        
        try {
            $this->updateByKey('db_hostname', $hostname);
            $this->updateByKey('db_database', $database);
            $this->updateByKey('db_username', $username);
            $this->updateByKey('db_password', $password);
        } catch (Exception $e) {}

        return true;
    }
    
    public function updateApiUrl($api_url)
    {
        return $this->updateByKey('api_url', $api_url);
    }
    
    public function getKey($key)
    {
        $key = $this->config->getByField('configuration', 'key', $key);
        if (isset($key)) {
            return $key['value'];
        }
        
        return false;
    }
}