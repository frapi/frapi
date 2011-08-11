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
class Default_Model_User extends Lupin_Model_DB
{
    /** 
     * A config object holding the Lupin_Config_Xml object
     * 
     * @var Lupin_Config_Xml $config  The config object.
     */
    protected $config;
    
    /** 
     * Constructor
     *
     * The constructor for the Users model
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = new Lupin_Config_Xml('users');
    }
    
    /** 
     * Add a new users
     *
     * This method is invoked whenever the user adding controller
     * is invoked. 
     *
     * @param array $data The data to create the user with.
     *
     * @return boolean true
     */
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

    /** 
     * Update a user
     *
     * This method updates a user using data passed 
     * to the $data method parameter.
     *
     * @param array $data The data array to update the user with.
     * @param string $id  An hash that contains the id of the user to update.
     * @return boolean true
     */
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

    /**
     * Delete a user
     *
     * This method deletes a user by it's hash-id
     *
     * @param  string $id The id of the user to delete.
     * @return void
     */
    public function delete($id)
    {
        $this->config->deleteByField('user', 'handle', $id);
    }

    /** 
     * Get a user
     *
     * This method is used to get a user by it's id.
     *
     * @param  string $id  The id of the user to get.
     * @return mixed Either boolean false or the user information.
     */
    public function getUser($id)
    {
        $user = $this->config->getByField('user', 'handle', $id);
        return isset($user) ? $user : false;
    }

    /** 
     * Checks if a handle exists
     *
     * This method is used to validate whether or not a handle is
     * available for a new user.
     *
     * @param  string $handle  The handle to validate.
     * @return boolean True if the user is free and false if the handle is taken.
     */
    public function handleAvailable($handle)
    {
        $user = $this->config->getByField('user', 'handle', $handle);
        if (isset($user) && $user !== false) {
            return false;
        }
        
        return true;
    }

    /**
     * Get all users
     *
     * Get all the users.
     *
     * @return mixed Either boolean false or an array of all users.
     */
    public function getAll()
    {
        $users = $this->config->getAll('user');
        return $users;
    }
}
