<?php
class Lupin_Config_Xml_Exception extends Exception {}
/**
 * Lupin_Config_Xml class
 *
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
 * This class deals with anything taht reads, saves, updates, delete
 * information in the configuration files. Needless to say, this is a
 * critical file.
 *
 * @license   New BSD
 * @package   Lupin
 */
class Lupin_Config_Xml
{
    /**
     * The configuration file location that is used when updating
     * and saving the xml file again at a later stage.
     *
     * @var string $configFile  The location of the xml configuration.
     */
    protected $configFile;

    /**
     * The array of the configuration
     *
     * @var array $config The configuration for a module.
     */
    protected $config;

    /**
     * This node disapears after the parse, however we need
     * to retain it for future use. IE(Saving)
     *
     * @var string  The name of the config
     */
    protected $configName;

    /**
     * Constructor
     *
     * This constructor fetches the local configuration path
     * and constructs the config file for a certain module.
     *
     * After having the xml configuration file loaded, it'll create
     * a Zend_Config_Xml instance and store it in $this->config
     *
     * @param string $name  The name of the configuration to load
     */
    public function __construct($name)
    {
        if (defined('ADMIN_CONF_PATH')) {
            $config = ADMIN_CONF_PATH;
        } else {
            try {
                $config = Zend_Registry::get('localConfigPath');
            } catch (Zend_Exception $e) {}
        }

        $this->configFile = $config . $name . '.xml';
        $this->configName = $name;

        /**
         * @TODO Remove me after the rewrite of the Config Reader. Move this to
         *       Frapi_Internal. I dislike inter-dependencies.
         */
        if ($cachedConfig = Frapi_Internal::getCached('configFile-' . $name))
        {
            $this->config = $cachedConfig;
        } else {
            $helper = new Lupin_Config_Helper_XmlArray();
            $this->config = $helper->parse($this->configFile);
            Frapi_Internal::setCached('configFile-' . $name, $this->config);
        }

        $this->config = $this->config[$name];
    }

    /**
     * Verify if the config file is still writeable.
     *
     * This method is used whenever we are about to write to
     * the config file. We validate whether or not we can
     * write to it and if we can't we simply throw an exception.
     *
     * @throws Exception
     * @return void
     */
    private function configIsWriteable()
    {
        if (!is_writeable($this->configFile)) {
            throw new Lupin_Config_Xml_Exception('File not writeable');
        }
    }

    /**
     * Get a single entry by field and value
     *
     * This method is used to retrieve a specific node
     * where a child node name has a specific value.
     *
     * @param  string $type      The node to retrieve.
     * @param  string $field     The field within that node.
     * @param  string $value     The expected value of that field to retrieve and delete
     * @return mixed  array|bool An array of information about what your config or false.
     */
    public function getByField($type, $field, $value)
    {
        if (isset($this->config[$type])) {
            if (!isset($this->config[$type][0])) {
                $this->config[$type] = array($this->config[$type]);
            }

            foreach ($this->config[$type] as $key => $node) {
                if (isset($node[$field]) && $node[$field] == $value &&
                    isset($this->config[$type][$key]) && isset($this->config[$type][$key][$field]))
                {
                    return $this->config[$type][$key];
                }
            }
        }

        return false;
    }

    /**
     * Get all entries of a type
     *
     * This method is used to retrieve all configuration
     * entries for a certain type.
     *
     * @param  string $type  The type or node name to retrieve.
     * @return array  An array of results ready to be abused.
     */
    public function getAll($type)
    {
        if (!isset($this->config[$type])) {
            return false;
        }

        $ret = $this->config[$type];

        if (empty($ret)) {
            return false;
        }

        return $this->normalize($ret);
    }

    /**
     * Delete a node from the configuration
     *
     * This API may seem a bit confusing, however the three
     * fields are used to identify the node we want to find,
     * the value of the current node and what to compare it against.
     *
     * Example (infos.xml):
     * ====================
     * <config>
     *   <infos>
     *     <name>
     *       <first>David</first>
     *     </name>
     *     <name>
     *       <first>Helgi</first>
     *     </name>
     *   </infos>
     * </config>
     *
     * <?php
     *     $conf = new Lupin_Config_Xml('infos');
     *     $conf->deleteByField('name', 'first', 'David');
     * ?>
     *
     * The result XML will be:
     *
     * <config>
     *   <infos>
     *     <name>
     *       <first>Helgi</first>
     *     </name>
     *   </infos>
     * </config>
     *
     *
     * @param  string $type   The node to retrieve.
     * @param  string $field  The field within that node.
     * @param  string $value  The expected value of that field to retrieve and delete
     *
     * @return bool           The result from the $this->save();
     */
    public function deleteByField($type, $field, $value)
    {
        if (isset($this->config[$type])) {
            if (!isset($this->config[$type][0])) {
                $this->config[$type] = array($this->config[$type]);
            }

            foreach ($this->config[$type] as $key => $node) {
                if (isset($node[$field]) && $node[$field] == $value &&
                    isset($this->config[$type][$key]))
                {
                    unset($this->config[$type][$key]);
                }

                // This is a bug that happens when there's no first key. We need
                // to make sure [0] exists in the array.
                if (isset($this->config[$type]) && !empty($this->config[$type]) &&
                    !isset($this->config[$type][0]))
                {
                    $conf = array_merge(array(), $this->config[$type]);
                    $this->config[$type] = $conf;
                }
            }
        }

        return $this->save();
    }

    /**
     * Edit an existing XML node
     *
     * This method is used to udpate the content of an xml node. What it does is
     * quite simple. It finds the ndoe you want, fetches the data from that node
     * merges it with the data you are passing and then saves it to xml.
     *
     * @param  string $type   The node to retrieve.
     * @param  string $field  The field within that node.
     * @param  string $value  The expected value of that field
     *                        to retrieve and delete
     * @param  array  $data   The data to update the node with.
     *
     * @return boolean Either false if the save was a failure
     *                 or true if it was a success
     */
    public function update($type, $field, $value, array $data)
    {
        if (isset($this->config[$type])) {
            if (!isset($this->config[$type][0])) {
                $this->config[$type] = array($this->config[$type]);
            }

            foreach ($this->config[$type] as $key => $node) {
                if (isset($node[$field]) && $node[$field] == $value &&
                    isset($this->config[$type][$key]))
                {
                    $values = $this->config[$type][$key];
                    $conf = array_merge($values, $data);

                    $this->config[$type][$key] = $conf;
                    return $this->save();
                }
            }
        }

        return false;
    }

    /**
     * Add a new node of data to the configuration
     *
     * This method is used to append/add new data into the xml
     * configuration files.
     *
     * By passing the array of fields, the data is going to be added
     * to the node of supplied "$type".
     *
     * @param string $type   The node to append to.
     * @param array  $data   The data of the new node to append to.
     *
     * @return mixed Either false or true depending on whether it was a
     *               success or a failure.
     */
    public function add($type, array $data)
    {
        if (empty($this->config)) {
            $this->config = array($type => true);
        }

        if (!isset($this->config[$type])) {
            $this->config[$type] = $type;
        }

        if (isset($this->config[$type])) {
            if (!isset($this->config[$type][0])) {
                $this->config[$type] = array($this->config[$type]);
            }
            // Generate a ... pseudo random hash.
            $data['hash'] = sha1(microtime(true) . mt_rand(1, 10000000));

            $len = count($this->config[$type]);
            $this->config[$type][$len] = $data;

            return $this->save();
        }

        return false;
    }

    /**
     * Save the new configuration
     *
     * This method saves the newly updated configuration. Please note
     * that at it's basic stage, the config files have a <config> root
     * node, however after saving using the Zend_Config_Writer_Xml, the
     * root node becomes <zend-config>.
     *
     * Don't worry about it, the parser won't see a difference.
     *
     * @throws Zend_Exception
     * @uses   Zend_Config_Writer_Xml
     * @return bool True of false whether the saving of the file worked or not.
     */
    protected function save()
    {
        $this->configIsWriteable();

        $helper = new Lupin_Config_Helper_XmlArray();

        Frapi_Internal::deleteCached('configFile-' . $this->configName);

        return $helper->write(
            array(
                'config'       => $this->config,
                'filename'     => $this->configFile,
                'topLevelName' => $this->configName,
            )
        );
    }

    /**
     * Normalize the output
     *
     * This method is used to normalize the output from the
     * Zend_Config object because it is not the same format
     * when there's only 1 key or multiple keys.
     *
     * For the sake of simplicity, we just make sure it's normalized
     * here and so we can use the output as we please without loads
     * of ifs everywhere.
     *
     * @param  Zend_Config $conf  The Zend_Config object to return
     * @return array An array of information about what your config.
     */
    private function normalize($conf)
    {
        $array = $conf;
        if (isset($array[0])) {
            return $array;
        }

        return array($array);
    }
}
