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

        if (isset($cache) && isset($cache['value'])) {
            $return[] = $cache;
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

        $this->refreshAPCCache();
        return true;
    }

    public function updateApiUrl($api_url)
    {
        return $this->updateByKey('api_url', $api_url);
    }

    public function updateLocale($locale)
    {
        return $this->updateByKey('locale', $locale);
    }

    public function getKey($key)
    {
        $key = $this->config->getByField('configuration', 'key', $key);
        if (isset($key)) {
            return $key['value'];
        }

        return false;
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

        $cache->delete($hash . '-Database.configs');
        $cache->delete($hash . '-configFile-configurations');
    }
}
