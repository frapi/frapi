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
        $engine   = $this->config->getByField('configuration', 'key', 'db_engine');

        if (isset($engine) && isset($engine['value'])) {
            $return[] = !empty($engine['value'])
                ? $engine : array('key' => $engine['key'], 'value' => '');
        }

        if (isset($hostname) && isset($hostname['value'])) {
            $return[] = !empty($hostname['value']) ?
                $hostname : array('key' => $hostname['key'], 'value' => '');
        }

        if (isset($database) && isset($database['value'])) {
            $return[] = !empty($database['value']) ?
                $database : array('key' => $database['key'], 'value' => '');
        }

        if (isset($username) && isset($username['value'])) {
            $return[] = !empty($username['value']) ?
                $username : array('key' => $username['key'], 'value' => '');
        }

        if (isset($password) && isset($password['value'])) {
            $return[] = !empty($password['value']) ?
                $password : array('key' => $password['key'], 'value' => '');
        }

        if (isset($cache) && isset($cache['value'])) {
            $return[] = !empty($cache['value'])
                ? $cache : array('key' => $cache['key'], 'value' => '');
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

    /**
     * Update an entry by key.
     *
     * This method is ubiquitously used thourough the
     * configuration model and is used to update a
     * configuration value by key.
     *
     * In the event where a configuration key does not
     * exist, the configuration key/value pair gets
     * created/added.
     *
     * @param  string $key   The key string value.
     * @param  string $value The value to the pair.
     *
     * @return  bool   The result of the update operation.
     */
    public function updateByKey($key, $value)
    {
        $res = $this->config->getByField(
            'configuration', 'key', $key
        );

        if ($res === false) {
            return $this->config->add(
                'configuration', array(
                    'key'  => $key,
                    'value' => $value,
                )
            );
        }

        $this->refreshAPCCache();

        return $this->config->update(
            'configuration', 'key', $key,
            array('value' => $value)
        );
    }

    /**
     * Edit the database settings.
     *
     * This method is used to edit and modify
     * the database configurations use by the
     * service.
     *
     * @param array $data An associative array of
     *                    the database configuration
     */
    public function editDb(array $data)
    {
        $engine   = $data['db_engine'];
        $hostname = $data['db_hostname'];
        $username = $data['db_username'];
        $password = $data['db_password'];
        $database = $data['db_database'];

        try {
            $this->updateByKey('db_engine',   $engine);
            $this->updateByKey('db_hostname', $hostname);
            $this->updateByKey('db_database', $database);
            $this->updateByKey('db_username', $username);
            $this->updateByKey('db_password', $password);
        } catch (Exception $e) {}

        $this->refreshAPCCache();
        return true;
    }

    /**
     * Update the "USE C DATA" configuration.
     *
     * This method is used to update the use cdata
     * configuration setting.
     *
     * @param string/bool $userCdata Whether or not to wrap
     *                    data returned to the user in a
     *                    CData block.
     *
     * @return bool       The result of the update operation
     *                    on the configuration file.
     */
    public function updateUseCdata($useCdata)
    {
        return $this->updateByKey('use_cdata', $useCdata);
    }

    /**
     * Update the "Allow X Domain Requests" configuration.
     *
     * This method is used to update the use x-requests
     * configuration setting.
     *
     * @param string/bool $allowX Whether or not to allow
     *                    cross-domain requests with FRAPI
     *
     * @return bool       The result of the update operation
     *                    on the configuration file.
     */
    public function updateAllowCrossDomain($allowX)
    {
        return $this->updateByKey(
            'allow_cross_domain', $allowX
        );
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
        if (isset($key) && isset($key['value'])) {
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
