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
class Default_Model_Error extends Lupin_Model
{
    protected $config;

    public function __construct()
    {
        $this->config = new Lupin_Config_Xml('errors');
    }

    public function add(array $data)
    {
        $whitelist = array('name', 'message', 'actions', 'description', 'http_code', 'http_phrase');
        $this->whiteList($whitelist, $data);

        $values = array(
            'name'        => $data['name'],
            'message'     => $data['message'],
            'http_code'   => $data['http_code'],
            'description' => $data['description'],
            'http_phrase' => $data['http_phrase'],
        );

        try {
            $this->config->add('error', $values);
        } catch (Exception $e) { }

        $this->refreshAPCCache();

        return true;
    }

    public function update(array $data, $id)
    {
        $whitelist = array('name', 'message', 'actions', 'description', 'http_code', 'http_phrase');
        $this->whiteList($whitelist, $data);

        $values = array(
            'name'        => $data['name'],
            'message'     => $data['message'],
            'http_code'   => isset($data['http_code']) ? $data['http_code'] : 400,
            'description' => $data['description'],
            'http_phrase' => $data['http_phrase'],
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
        $configModel = new Default_Model_Configuration();
        $server = $configModel->getKey('api_url');
        $hash = isset($server) ? hash('sha1', $server) : '';

        $cache = Frapi_Cache::getInstance(FRAPI_CACHE_ADAPTER);

        $cache->delete($hash . '-Errors.user-defined');
        $cache->delete($hash . '-configFile-errors');
    }
}