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
class Default_Model_Action extends Lupin_Model
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
        $this->config = new Lupin_Config_Xml('actions');
    }

    /**
     * Add a new action
     *
     * This method is invoked whenever the action adding controller
     * is invokved. It calls $this->generateAction() and creates a
     * new action file if it doesn't exist.
     *
     * @param array $data The data to create the action with.
     *
     * @return boolean true
     */
    public function add(array $data)
    {
        $whitelist = array(
            'name',  'enabled',  'description',  'public',
            'param', 'required', 'route', 'use_custom_route'
        );

        $this->whiteList($whitelist, $data);

        // Replace spaces with underscores, to attempt to make it a valid name
        $data['name'] = ucfirst(strtolower(str_replace(' ', '_', $data['name'])));

        // Validate the action doesn't already exist and is valid
        if (in_array($data['name'], $this->getList())) {
            throw new RuntimeException('There is already an action with this name.');
        } else if (!preg_match('/^[A-Z][a-z0-9\_\-]+$/', $data['name'])) {
            throw new RuntimeException('Action name does not validate. Please ensure it contains only alpha-numeric characters, underscores and dashes.');
        }

        // Routes can only be lower case!
        $data['route'] = strtolower($data['route']);

        // Validate the route does not already exist and is valid
        $router = new Frapi_Router();
        $router->loadAndPrepareRoutes();
        if ($router->match($data['route'])) {
            throw new RuntimeException('There is already an action with this route.');
        }

        $segments = Frapi_Router::parseSegments($data['route']);
        foreach ($segments as $key => $value) {
            if (!empty($value)) {
                if ($key == 0) {
                    if (!preg_match('/^[a-z0-9\-\_\*]+$/', $value)) {
                        throw new RuntimeException('Action route does not validate. Please ensure each part contains only alpha-numeric characters, underscores, dashes and colons.');
                    }
                } else {
                    if (!preg_match('/^:?[a-z0-9\-\_\*]+$/', $value)) {
                        throw new RuntimeException('Action route does not validate. Please ensure each part contains only alpha-numeric characters, underscores, dashes and colons.');
                    }
                }
            }
        }

        $values = array(
            'name'        =>  $data['name'],
            'enabled'     =>  $data['enabled'],
            'public'      =>  $data['public'],
            'description' =>  $data['description'],
            'route'       =>  $data['route'],
        );

        $values['parameters'] = array();

        if (isset($data['param'])) {

            if (isset($data['required']) && is_array($data['required'])) {
                 foreach ($data['required'] as $key => $value) {
                     if (isset($data['param'][$key])) {
                        $values['parameters']['parameter'][] = array(
                                'name'     => $data['param'][$key],
                                'required' => '1',
                        );

                        unset($data['param'][$key]);
                     }
                 }
            }

            foreach ($data['param'] as $param => $value) {
                $values['parameters']['parameter'][] = array(
                    'name'     => $value,
                    'required' => '0',
                );
            }

        }

        /*
         * If we have no parameters we still need a <parameters> entry in the
         * config file.
         */
        if (!count($values['parameters'])) {
            $values['parameters'] = '';
        }

        $this->config->add('action', $values);

        $this->refreshAPCCache();
        return true;
    }

    /**
     * Update an action
     *
     * This method updates an action using data passed
     * to the $data method parameter.
     *
     * @param array $data The data array to update the action with.
     * @param string $id  An hash that contains the id of the action to update.
     * @return boolean true
     */
    public function update(array $data, $id)
    {
        $whitelist = array(
            'name',  'enabled',  'description',  'public',
            'param', 'required', 'route', 'use_custom_route'
        );

        $this->whiteList($whitelist, $data);

        // Replace spaces with underscores, to attempt to make it a valid name
        $data['name'] = ucfirst(strtolower(str_replace(' ', '_', $data['name'])));

        // Validate the action doesn't already exist and is a valid name
        $tempAction = $this->get($id);
        if ($tempAction['name'] != $data['name'] && in_array($data['name'], $this->getList())) {
            throw new RuntimeException('There is already an action with this name.');
        } else if(!preg_match('/^[A-Z][a-zA-Z0-9\_\-]+$/', $data['name'])) {
            throw new RuntimeException('Action name does not validate. Please ensure it contains only alpha-numeric characters, underscores and dashes.');
        }

        // Routes can only be lower case!
        $data['route'] = strtolower($data['route']);

        // Validate the route does not already exist and is valid
        if ($tempAction['route'] != $data['route']) {

            // Validate the route does not already exist and is valid
            $router = new Frapi_Router();
            $router->loadAndPrepareRoutes();
            if ($router->match($data['route'])) {
                throw new RuntimeException('There is already an action with this route.');
            }
        }

        $segments = Frapi_Router::parseSegments($data['route']);
        foreach ($segments as $key => $value) {
            if (!empty($value)) {
                if ($key == 0) {
                    if (!preg_match('/^[a-z0-9\-\_\*]+$/', $value)) {
                        throw new RuntimeException('Action route does not validate. Please ensure each part contains only alpha-numeric characters, underscores, dashes and colons.');
                    }
                } else {
                    if (!preg_match('/^:?[a-z0-9\-\_\*]+$/', $value)) {
                        throw new RuntimeException('Action route does not validate. Please ensure each part contains only alpha-numeric characters, underscores, dashes and colons.');
                    }
                }
            }
        }

        $values = array(
            'name'        =>  $data['name'],
            'enabled'     =>  $data['enabled'],
            'public'      =>  $data['public'],
            'description' =>  $data['description'],
            'route'       =>  $data['route'],
        );

        $values['parameters'] = array();

        if (isset($data['param'])) {
            $params = $data['param'];

            foreach ($params as $param => $value) {
                if (strlen(trim($data['param'][$param])) <= 0) {
                    continue;
                }

                $values['parameters']['parameter'][] = array(
                    'name'     => $data['param'][$param],
                    'required' => (isset($data['required'][$param]) ? '1' : '0'),
                );
            }
        }

        /*
         * If we have no parameters we still need a <parameters> entry in the
         * config file.
         */
        if (!count($values['parameters'])) {
            $values['parameters'] = '';
        }

        try {
            $this->config->update('action', 'hash', $id, $values);
        } catch (Exception $e) {}

        $this->refreshAPCCache();
        return true;
    }

    /**
     * Delete an action
     *
     * This method deletes an action by it's hash-id
     *
     * @param  string $id The id of the action to delete.
     * @return void
     */
    public function delete($id)
    {
        $this->config->deleteByField('action', 'hash', $id);
        $this->refreshAPCCache();
    }

    /**
     * Get an action
     *
     * This method is used to retrieve information about an
     * action using it's hash-id.
     *
     * @param  string $id The id of the action to retrieve.
     * @return mixed  Either an array with all the information related
     *                to an action or a boolean false when nothing is found.
     */
    public function get($id)
    {
        $action = $this->config->getByField('action', 'hash', $id);
        return isset($action) ? $action : false;
    }

    /**
     * Get a list of actions
     *
     * This method is used to retrieve a list of actions.
     *
     * @return array An array of actions by hashes.
     */
    public function getList()
    {
        $action = $this->config->getAll('action');

        $return = array();
        foreach ($action as $key => $d) {
            $return[$d['hash']] = $d['name'];
        }

        return $return;
    }

    /**
     * Get all action
     *
     * Get all the actions.
     *
     * @return mixed Either boolean false or an array of all actions.
     */
    public function getAll()
    {
        $action = $this->config->getAll('action');
        return $action;
    }

    /**
     * Synchronize the code base
     *
     * This method is invoked whenever someone clicks on the "Sync" button. It
     * looks at the actions, looks at the existing files and either generate
     * the action if the action file doesn't exist or updates it whenever
     * it has to be updated (New required parameters, etc).
     *
     * @return void
     */
    public function sync()
    {
        $actions = $this->config->getAll('action');

        foreach ($actions as $key => $a) {
            $description = isset($a['description']) ? $a['description'] : false;
            $route = isset($a['route']) ? $a['route'] : false;

            if (empty($a)) {
                continue;
            }

            $params = array();

            if (isset($a['parameters']['parameter'])) {
                $params = $a['parameters']['parameter'];
            }

            if (count($params) && !isset($params[0])) {
                $params = array($params);
            }

            $p = array();
            if (!empty($params)) {
                foreach ($params as $param) {
                    if ($param['required'] == '1') {
                        $p[] = $param['name'];
                    }
                }
            }

            $name = ucfirst(strtolower($a['name']));
            $dir  = CUSTOM_PATH . DIRECTORY_SEPARATOR . 'Action';

            $file = $dir . DIRECTORY_SEPARATOR . $name . '.php';

            if (!file_exists($file)) {
                $content = $this->generateAction($name, $p, $description, $route);
            } else {
                $content = $this->updateAction($file, $name, $p);
            }

            if ($content !== false && $content !== null && strlen($content) > 8) {
                // Do atomic move
                $tempName = $name . sha1(mt_rand(1, 100000000) . microtime(true)) . '.php';
                file_put_contents($dir . DIRECTORY_SEPARATOR . $tempName, $content);
                rename($dir . DIRECTORY_SEPARATOR . $tempName, $file);
                chmod($file, 0777);
            }
        }

        $this->refreshAPCCache();
    }

    /**
     * Update an action
     *
     * This method is used whenever an action is updated from the
     * administration panel.
     *
     * @param string $file  The file to update.
     * @param string $name  The name of the file to update.
     * @param array  $properties An array of properties related to an action.
     *
     * @return Either a generated/updated action or false.
     */
    private function updateAction($file, $name, $properties)
    {
        $className = 'Action_' . $name;
        if (!class_exists($className)) {
            include $file;
        }

        // In case the file is empty or messed up, lets generate it again
        if (!class_exists($className)) {
            return $this->generateAction($name, $properties);
        }

        try {
            $class = Zend_CodeGenerator_Php_Class::fromReflection(
                new Zend_Reflection_Class($className)
            );

            $default = $class->getProperty('requiredParams');
            if (is_object($default) && ($default->getDefaultValue()->getValue() != $properties)) {
                $default->setSourceDirty(true);
                $class->setProperty($default->setDefaultValue($properties));
            }

            $file = new Zend_CodeGenerator_Php_File();
            $file->setClass($class);
        } catch (Exception $e) {
            return false;
        }

        return $file->generate();
    }

    /**
     * Generate the action
     *
     * This is a gigantic method used to generate the actual Action code. It
     * uses the properties, description, name, and routes passed to it.
     *
     * This method uses the Zend_CodeGenerator_Php_* family to identify and create the
     * new files.
     *
     * @param string $name  The name of the action
     * @param array $properties An array of properties related to an action
     * @param string $description A description for an action. Default false.
     * @param string $route The custom route for that action. Default false.
     *
     * @return string A generated file.
     */
    private function generateAction($name, $properties, $description = false, $route = false)
    {
        $docblock = new Zend_CodeGenerator_Php_Docblock(array(
            'shortDescription' => 'Required parameters',
            'tags'             => array(
                new Zend_CodeGenerator_Php_Docblock_Tag(array(
                    'name'       => 'var',
                    'datatype'   => 'array',
                    'description' => 'An array of required parameters.',
                )),
            ),
        ));

        $class = new Zend_CodeGenerator_Php_Class();
        $class->setName('Action_' . $name);
        $class->setExtendedClass('Frapi_Action');
        $class->setImplementedInterfaces(array('Frapi_Action_Interface'));

        $tags = array(
            array(
                'name'        => 'link',
                'description' => 'http://getfrapi.com',
            ),
            array(
                'name'        => 'author',
                'description' => 'Frapi <frapi@getfrapi.com>',
            )
        );

        if ($route !== false) {
            $tags[] = array('name' => 'link', 'description' => $route);
        }

        $classDocblock = new Zend_CodeGenerator_Php_Docblock(array(
                'shortDescription' => 'Action ' . $name . ' ',
                'longDescription'  => ($description !== false)
                                      ? $description : 'A class generated by Frapi',
                'tags'             => $tags,
            )
        );

        $class->setDocblock($classDocblock);
        $class->setProperties(array(
            array(
                'name'         => 'requiredParams',
                'visibility'   => 'protected',
                'defaultValue' => $properties,
                'docblock'     => $docblock,
            ),
            array(
                'name'         => 'data',
                'visibility'   => 'private',
                'defaultValue' => array(),
                'docblock'     => new Zend_CodeGenerator_Php_Docblock(array(
                    'shortDescription' => 'The data container to use in toArray()',
                    'tags'             => array(
                        new Zend_CodeGenerator_Php_Docblock_Tag(array(
                            'name'        => 'var',
                            'datatype'    => 'array',
                            'description' => 'A container of data to fill and return in toArray()'
                        )),
                    ),
                )),
            ),
        ));

        $methods = array();

        $docblock = new Zend_CodeGenerator_Php_Docblock(array(
            'shortDescription' => 'To Array',
            'longDescription'  => "This method returns the value found in the database \n" .
                                  'into an associative array.',

            'tags'             => array(
                new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
                    'datatype'  => 'array',
                )),
            ),
        ));

        $toArrayBody = '        ' . "\n";
        if (!empty($properties)) {
            foreach ($properties as $p) {
                $toArrayBody  .=
                    '$this->data[\'' . $p . '\'] = ' .
                    '$this->getParam(\'' . $p . '\', self::TYPE_OUTPUT);' . "\n";
            }
        }

        $toArrayBody.= 'return $this->data;';

        $methods[] = new Zend_CodeGenerator_Php_Method(array(
                            'name' => 'toArray',
                            'body' => $toArrayBody,
                            'docblock' => $docblock
                        ));

        $executeActionBody = '';
        if (!empty($properties)) {
            $executeActionBody = '        $valid = $this->hasRequiredParameters($this->requiredParams);
if ($valid instanceof Frapi_Error) {
    throw $valid;
}';
        }

        $executeActionBody .= "\n\n" . 'return $this->toArray();';

        $docblockArray = array(
            'shortDescription' => '',
            'longDescription'  => '',
            'tags'             => array(
                new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
                    'datatype'  => 'array',
                )),
            )
        );

        $docblock = new Zend_CodeGenerator_Php_Docblock(array());

        $docblockArray['shortDescription'] = 'Default Call Method';
        $docblockArray['longDescription']  = 'This method is called when no specific ' .
                                             'request handler has been found';

        $methods[] = new Zend_CodeGenerator_Php_Method(array(
            'name' => 'executeAction',
            'body' => $executeActionBody,
            'docblock' => $docblockArray,
        ));

        $docblockArray['shortDescription'] = 'Get Request Handler';
        $docblockArray['longDescription']  = 'This method is called when a request is a GET';
        $methods[] = new Zend_CodeGenerator_Php_Method(array(
            'name' => 'executeGet',
            'body' => $executeActionBody,
            'docblock' => $docblockArray,
        ));

        $docblockArray['shortDescription'] = 'Post Request Handler';
        $docblockArray['longDescription']  = 'This method is called when a request is a POST';
        $methods[] = new Zend_CodeGenerator_Php_Method(array(
            'name' => 'executePost',
            'body' => $executeActionBody,
            'docblock' => $docblockArray,
        ));

        $docblockArray['shortDescription'] = 'Put Request Handler';
        $docblockArray['longDescription']  = 'This method is called when a request is a PUT';
        $methods[] = new Zend_CodeGenerator_Php_Method(array(
            'name' => 'executePut',
            'body' => $executeActionBody,
            'docblock' => $docblockArray,
        ));

        $docblockArray['shortDescription'] = 'Delete Request Handler';
        $docblockArray['longDescription']  = 'This method is called when a request is a DELETE';
        $methods[] = new Zend_CodeGenerator_Php_Method(array(
            'name' => 'executeDelete',
            'body' => $executeActionBody,
            'docblock' => $docblockArray,
        ));

        $docblockArray['shortDescription'] = 'Head Request Handler';
        $docblockArray['longDescription']  = 'This method is called when a request is a HEAD';
        $methods[] = new Zend_CodeGenerator_Php_Method(array(
            'name' => 'executeHead',
            'body' => $executeActionBody,
            'docblock' => $docblockArray,
        ));

        $class->setMethods($methods);

        $file = new Zend_CodeGenerator_Php_File();
        $file->setClass($class);
        return $file->generate();
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

        $cache->delete($hash . '-Actions.enabled-public');
        $cache->delete($hash . '-Actions.enabled-private');
        $cache->delete($hash . '-Router.routes-prepared');
        $cache->delete($hash . '-configFile-actions');
    }
}
