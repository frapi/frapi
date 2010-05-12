<?php
class Frapi_Database_Exception extends Frapi_Exception {}

/**
 * Database
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
 * This context is the one that is going to be executing
 * the database interactions through the web service.
 *
 * @uses      Frapi_Database_Exception
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Database extends PDO
{
    /**
     * Just the instance of the PDO connection.
     *
     * @var PDO object
     */
    private static $instance;
    
    /**
     * Singleton for database connections
     *
     * This method is going to verify if there's an existing instance
     * of a database object and if so will return it.
     *
     *
     * @return PDO A new PDO object or an existing PDO object
     */
    protected static function singleton()
    {
        self::$instance = null;
        if (!isset(self::$instance)) {
            $configs = self::getDbConfig();

            self::$instance = new PDO(
                'mysql:dbname='.$configs['db_database'].';host='.$configs['db_hostname'], 
                $configs['db_username'], 
                $configs['db_password']
            );
        }
        
        return self::$instance;
    }
    
    /**
     * Get a database instance
     *
     * This method is used to retrieve a database instance. For the sake
     * of simplicity we are using PDO by default. Thus returning a PDO
     * object (or whichever of it's kind)
     *
     *
     * @return mixed PDO* An object of type PDO
     */
    public static function getInstance()
    {
        return self::singleton();
    }

    
    private static function getDbConfig()
    {
        throw new Frapi_Database_Exception('This method is outdated. We have to fix it.');
        
        $sql    = "
            SELECT key, value 
            FROM configurations
            WHERE key IN ('db_hostname', 'db_username', 'db_database', 'db_password')
        ";
        
        $configs = Frapi_Internal::getDB()->query($sql)->fetchAll();
        
        $conf = array();
        foreach ($configs as $key => $value) {
            $conf[$value['key']] = $value['value'];
        }
        
        unset($configs);
        unset($db);
        
        return $conf;
    }
    
    public static function getMasterInstance()
    {
        throw new Frapi_Database_Exception('Method not yet implemented');
    }
    
    public static function getSlavesInstance()
    {
        throw new Frapi_Database_Exception('Method not yet implemented');
    }
}