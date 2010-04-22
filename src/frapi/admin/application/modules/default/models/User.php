<?php
class Default_Model_User extends Lupin_Model_DB
{
    protected $config;
    
    public function __construct()
    {
        $this->config = new Lupin_Config_Xml('users');
    }
    
    public function add(array $data)
    {
        if (!$this->handleAvailable($data['handle'])) {
            return false;
        }

        $whitelist = array('handle', 'password');
        $this->whitelist($whitelist, $data);

        // I'd like to create a configurable salt here for when
        // people go through the install wizard, a salt gets defined
        // in order to insure more safety for their users passwords.
        $values = array(
            'handle'  => $data['handle'], 
            'password'=> sha1($data['password']),
            
            // Those two will be used eventually.
            'active'  => 1,
            'role'    => 'admin',
        );
        
        try {
            $this->config->add('user', $values);
        } catch (Exception $e) { }
        return true;
    }

    public function update(array $data, $id)
    {
        $whitelist = array('password');
        $this->whitelist($whitelist, $data);

        $values = array('password'=> sha1($data['password']));

        try {
            $this->config->update('user', 'handle', $id, $values);
        } catch (Exception $e) { }

        return true;
    }

    public function delete($id)
    {
        $this->config->deleteByField('user', 'handle', $id);
    }

    public function getUser($id)
    {
        $user = $this->config->getByField('user', 'handle', $id);
        return isset($user) ? $user : false;
    }

    public function handleAvailable($handle)
    {
        $user = $this->config->getByField('user', 'handle', $handle);
        if (isset($user)) {
            return false;
        }
        
        return true;
    }

    public function getAll()
    {
        $users = $this->config->getAll('user');
        return $users;
    }
}