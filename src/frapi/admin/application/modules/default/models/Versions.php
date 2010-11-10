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
class Default_Model_Versions extends Lupin_Model_DB
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
        $this->config = new Lupin_Config_Xml('versions');
    }
    
    /** 
     * Add a new users
     *
     * This method is invoked whenever the version adding controller
     * is invoked. 
     *
     * @param array $data The data to create the version with.
     *
     * @return boolean true
     */
    public function add(array $data)
    {
        if (!$this->nameAvailable($data['name'])) {
            return false;
        }

        $whitelist = array('name', 'value', 'urlPrefix');
        $this->whitelist($whitelist, $data);

        $values = array(
            'name'      => $data['name'], 
            'value'     => $data['value'],
            'urlPrefix' => $data['urlPrefix'],
            'hash'      => sha1($data['name'] . time() . mt_rand(1, 100000)),
        );

        try {
            $this->config->add('version', $values);
        } catch (Exception $e) { }
        return true;
    }

    /** 
     * Update a user
     *
     * This method updates a version using data passed 
     * to the $data method parameter.
     *
     * @param array $data The data array to update the version with.
     * @param string $id  An hash that contains the id of the version to update.
     * @return boolean true
     */
    public function update(array $data, $id)
    {
        $whitelist = array('value', 'urlPrefix');
        $this->whitelist($whitelist, $data);

        $values = array('value' => $data['value'], 'urlPrefix' => $data['urlPrefix']);

        try {
            $this->config->update('version', 'hash', $id, $values);
        } catch (Exception $e) { }

        return true;
    }

    /**
     * Delete a version
     *
     * This method deletes a version by it's hash-id
     *
     * @param  string $id The id of the version to delete.
     * @return void
     */
    public function delete($id)
    {
        $this->config->deleteByField('version', 'hash', $id);
    }

    /** 
     * Get a version
     *
     * This method is used to get a version by it's id.
     *
     * @param  string $id  The id of the version to get.
     * @return mixed Either boolean false or the version information.
     */
    public function getVersion($id)
    {
        $version = $this->config->getByField('version', 'hash', $id);
        return isset($version) ? $version : false;
    }

    /** 
     * Checks if a name exists
     *
     * This method is used to validate whether or not a name is
     * available for a new  version.
     *
     * @param  string $name  The name to validate.
     * @return boolean True if the version name is free and false if the handle is taken.
     */
    public function nameAvailable($name)
    {
        $name = $this->config->getByField('version', 'name', $name);

        if (isset($name) && $name !== false) {
            return false;
        }
        
        return true;
    }

    /**
     * Get all versions
     *
     * Get all the versions.
     *
     * @return mixed Either boolean false or an array of all versions.
     */
    public function getAll()
    {
        $versions = $this->config->getAll('version');
        return $versions;
    }
}
