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

        $cache->delete($hash . '-Basic.configs');
        $cache->delete($hash . '-configFile-configurations');
    }
}
