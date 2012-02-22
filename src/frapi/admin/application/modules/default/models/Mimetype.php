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
class Default_Model_Mimetype extends Lupin_Model
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
     * The constructor for the Action model
     *
     * @return void
     */
    public function __construct()
    {
        $file = Zend_Registry::get('localConfigPath');
        $file = $file . 'mimetypes.xml';
        
        if (!file_exists($file)) {
            // Special case to upgrade legacy users
            $this->config = $this->upgradeConfig($file);
            return;
        }
        
        $this->config = new Lupin_Config_Xml('mimetypes');
    }

    /**
     * Add a new mimetype
     *
     * This method is invoked whenever the mimetype adding controller
     * is invoked. It calls $this->generateMimetype() and creates a
     * new mimetype file if it doesn't exist.
     *
     * @param array $data The data to create the mimetype with.
     *
     * @return boolean true
     */
    public function add(array $data)
    {
        $whitelist = array(
            'mimetype',  'output_format',  'description'
        );

        $this->whiteList($whitelist, $data);

        // Mimetypes should be lowercase
        $data['mimetype'] = strtolower($data['mimetype']);

        // Validate the mimetype doesn't already exist and is valid
        if (in_array($data['mimetype'], $this->getList())) {
            throw new RuntimeException('Mimetype already exists.');
        } else if (!preg_match('/^:?[a-z]+\/:?[a-z]([:a-z0-9\-\.]+)?(\+[:a-z0-9\-\.]+)?$/', $data['mimetype'])) {
            throw new RuntimeException('Mimetype does not validate.');
        }
        
        // Validate the output_format is present
        if (!isset($data['output_format']) || empty($data['output_format'])) {
            throw new RuntimeException('Output Format is required.');
        }

        $values = array(
            'mimetype'        =>  $data['mimetype'],
            'output_format'     =>  $data['output_format'],
            'description'     =>  $data['description'],
        );

        $this->config->add('mimetype', $values);

        $this->refreshAPCCache();
        return true;
    }

    /**
     * Update an mimetype
     *
     * This method updates an mimetype using data passed
     * to the $data method parameter.
     *
     * @param array $data The data array to update the mimetype with.
     * @param string $id  An hash that contains the id of the mimetype to update.
     * @return boolean true
     */
    public function update(array $data, $id)
    {
       $whitelist = array(
            'mimetype',  'output_format',  'description'
        );

        $this->whiteList($whitelist, $data);

        // Mimetypes should be lowercase
        $data['mimetype'] = strtolower($data['mimetype']);

        // Validate the mimetype is valid
        if (!preg_match('/^[a-z]+\/[a-z]([a-z0-9\-\.]+)?(\+[a-z0-9\-\.]+)?$/', $data['mimetype'])) {
            throw new RuntimeException('Mimetype does not validate.');
        }
        
        // Validate the output_format is present
        if (!isset($data['output_format']) || empty($data['output_format'])) {
            throw new RuntimeException('Output Format is required.');
        }

        $values = array(
            'mimetype'        =>  $data['mimetype'],
            'output_format'     =>  $data['output_format'],
            'description'     =>  $data['description'],
        );

        try {
            $this->config->update('mimetype', 'hash', $id, $values);
        } catch (Exception $e) {}

        $this->refreshAPCCache();
        return true;
    }

    /**
     * Delete a mimetype
     *
     * This method deletes a mimetype by it's hash-id
     *
     * @param  string $id The id of the mimetype to delete.
     * @return void
     */
    public function delete($id)
    {
        $this->config->deleteByField('mimetype', 'hash', $id);
        $this->refreshAPCCache();
    }

    /**
     * Get a mimetype
     *
     * This method is used to retrieve information about a
     * mimetype using it's hash-id.
     *
     * @param  string $id The id of the mimetype to retrieve.
     * @return mixed  Either an array with all the information related
     *                to a mimetype or a boolean false when nothing is found.
     */
    public function get($id)
    {
        $mimetype = $this->config->getByField('mimetype', 'hash', $id);
        return isset($mimetype) ? $mimetype : false;
    }

    /**
     * Get a list of mimetypes
     *
     * This method is used to retrieve a list of mimetypes.
     *
     * @return array An array of mimetypes by hashes.
     */
    public function getList()
    {
        $mimetypes = $this->config->getAll('mimetype');

        $return = array();
        foreach ($mimetypes as $key => $d) {
            $return[$d['hash']] = $d['mimetype'];
        }

        return $return;
    }

    /**
     * Get all mimetypes
     *
     * Get all the mimetypes.
     *
     * @return mixed Either boolean false or an array of all mimetypes.
     */
    public function getAll()
    {
        $mimetypes = $this->config->getAll('mimetype');
        return $mimetypes;
    }

    /**
     * Refresh the APC cache by deleting APC entries.
     *
     * @return void
     */
    public function refreshAPCCache()
    {
        $configModel = new Default_Model_Configuration();
        $server = $configModel->getKey('api_url');
        $hash = isset($server) ? hash('sha1', $server) : '';

        $cache = Frapi_Cache::getInstance(FRAPI_CACHE_ADAPTER);

        $cache->delete($hash . '-configFile-mimetypes');
    }
    
    private function upgradeConfig($file)
    {
        file_put_contents($file, '<frapi-config><mimetypes/></frapi-config>');
        $config = new Lupin_Config_Xml('mimetypes');
        
        $mimetypes = array(
            'application/xml'  => 'xml',
            'text/xml'         => 'xml',
            'application/json' => 'json',
            'text/json'        => 'json',
            'text/html'        => 'html',
            'text/plain'       => 'json',
            'text/javascript'  => 'js',
            'text/php-printr'  => 'printr'
        );
        
        foreach ($mimetypes as $mimetype => $format) {
            $data = array(
                'mimetype' => $mimetype,
                'output_format' => $format,
                'description' => ''
            );
            
            $config->add('mimetype', $data);
        }
        
        return $config;
    }
}
