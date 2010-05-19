<?php

class Default_Model_Error extends Lupin_Model
{
    protected $config;
    
    public function __construct()
    {
        $this->config = new Lupin_Config_Xml('errors');
    }
    
    public function add(array $data)
    {
        $whitelist = array('name', 'message', 'actions', 'description', 'http_code');
        $this->whiteList($whitelist, $data);

        $values = array(
            'name'        => $data['name'], 
            'message'     => $data['message'], 
            'http_code'   => $data['http_code'],
            'description' => $data['description'], 
        );
        
        try {
            $this->config->add('error', $values);
        } catch (Exception $e) { }
        
        $this->refreshAPCCache();
        
        return true;
    }

    public function update(array $data, $id)
    {
        $whitelist = array('name', 'message', 'actions', 'description', 'http_code');
        $this->whiteList($whitelist, $data);

        $values = array(
            'name'        => $data['name'], 
            'message'     => $data['message'], 
            'http_code'   => isset($data['http_code']) ? $data['http_code'] : 400,
            'description' => $data['description'], 
        );
        
        try {
            $this->config->update('error', 'hash', $id, $values);
        } catch (Exception $e) { }
        
        $this->refreshAPCCache();        
        
        return true;
    }

    public function delete($id)
    {
        $this->config->deleteByField('error', 'hash', $id);
        $this->refreshAPCCache();        
    }

    public function get($id)
    {
        $error = $this->config->getByField('error', 'hash', $id);
        return isset($error) ? $error : false;
    }
    
    public function getAll()
    {
        $errors = $this->config->getAll('error');
        return $errors;
    }
    
    /**
     * Refresh the APC cache by deleting APC entry.
     *
     * @return void
     */
    public function refreshAPCCache()
    {
        apc_delete('Errors.user-defined');
    }
}