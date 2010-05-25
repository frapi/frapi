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
    protected $config;
    
    public function __construct()
    {
        $this->config = new Lupin_Config_Xml('actions');
    }

    public function add(array $data)
    {
        $whitelist = array(
            'name',  'enabled',  'description',  'public',
            'param', 'required', 'route', 'use_custom_route'
        );
        
        $this->whiteList($whitelist, $data);

        $values = array(
            'name'        =>  $data['name'],
            'enabled'     =>  $data['enabled'],
            'public'      =>  $data['public'],
            'description' =>  $data['description'],
            'route'       =>  isset($data['use_custom_route']) ? $data['route'] : null
        );
        
        if (isset($data['param']) && isset($data['required'])) {
            $params = array_combine($data['param'], $data['required']);
            
            $values['parameters'] = array();
            
            foreach ($params as $param => $value) {
                $values['parameters']['parameter'][] = array(
                    'name'     => $param,
                    'required' => ($value == 'on' ? '1' : '0'),
                );
            }
        }
        
        $this->config->add('action', $values);
        
        $this->refreshAPCCache();
        return true;
    }

    public function update(array $data, $id)
    {
        $whitelist = array(
            'name',  'enabled',  'description',  'public',
            'param', 'required', 'route', 'use_custom_route'
        );
        
        $this->whiteList($whitelist, $data);

        $values = array(
            'name'        =>  $data['name'],
            'enabled'     =>  $data['enabled'],
            'public'      =>  $data['public'],
            'description' =>  $data['description'],
            'route'       =>  $data['use_custom_route'] ? $data['route'] : null
        );
        
        
        if (isset($data['param']) && isset($data['required'])) {
            $params = array_combine($data['param'], $data['required']);
            
            $values['parameters'] = array();
            
            foreach ($params as $param => $value) {
                $values['parameters']['parameter'][] = array(
                    'name'     => $param,
                    'required' => ($value == 'on' ? '1' : '0'),
                );
            }
        }
        
        try {
            $this->config->update('action', 'hash', $id, $values);
        } catch (Exception $e) { }

        $this->refreshAPCCache();
        return true;
    }

    public function delete($id)
    {
        $this->config->deleteByField('action', 'hash', $id);

        $this->refreshAPCCache();
    }

    public function get($id)
    {        
        $action = $this->config->getByField('action', 'hash', $id);
        return isset($action) ? $action : false;
    }

    public function getList()
    {
        $action = $this->config->getAll('action');

        $return = array();
        foreach ($action as $key => $d) {
            $return[$d['hash']] = $d['name'];
        }

        return $return;
    }

    public function getAll()
    {
        $action = $this->config->getAll('action');

        return $action;
    }

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
            $params = $a['parameters']['parameter'];
            
            if (isset($params) && !isset($params[0])) {
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
            $dir  = ROOT_PATH . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'Action';

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
    }

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
            if ($default->getDefaultValue()->getValue() != $properties) {
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
                'longDescription'  => ($description !== false) ? $description : 'A class generated by Frapi',
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
            'longDescription'  => "This method returns the value found in the database \ninto an associative array.",
            'tags'             => array(
                new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
                    'datatype'  => 'array',
                )),
            ),
        ));

        $toArrayBody = '        ' . "\n";
        if (!empty($properties)) {
            foreach ($properties as $p) {
                $toArrayBody.= '$this->data[\'' . $p . '\'] = $this->getParam(\'' . $p . '\', self::TYPE_OUTPUT);' . "\n";
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
    return $valid;
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
        $docblockArray['longDescription']  = 'This method is called when no specific request handler has been found';
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
        
        apc_delete($hash . '-Actions.enabled-public');
        apc_delete($hash . '-Actions.enabled-private');
        apc_delete($hash . '-Router.routes-prepared');
    }
}
