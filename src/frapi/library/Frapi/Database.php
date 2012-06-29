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
 * @package   frapi
 */
class Frapi_Database extends PDO
{
    /**
     * Just the instance of the PDO connection.
     *
     * @var PDO object
     */
    private static $instance = array();

    /**
     * Singleton for database connections
     *
     * This method is going to verify if there's an existing instance
     * of a database object and if so will return it.
     *
     * @param  string $name The name of the instantiation (namespace emulation)
     *                      This parameter is optional and is defaulted to 'default'
     *
     * @return PDO A new PDO object or an existing PDO object
     */
    protected static function factory($name = 'default')
    {
        if (!isset(self::$instance[$name])) {
            $configs = Frapi_Internal::getCachedDbConfig();

            $dsn = self::buildDsn($configs);

            // I iz not happy with this. We already have a switch
            // for the dsn in the "buildDsn" method...
            if (isset($configs['db_engine']) &&
                in_array($configs['db_engine'], array('pgsql')))
            {
                // DSN that have the user/pass implicitely defined.
                self::$instance[$name] = new PDO($dsn);
            } else {
                // Other dsns like mysql, mssql, etc.
                self::$instance[$name] = new PDO(
                    $dsn,
                    $configs['db_username'],
                    $configs['db_password'],
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\'')
                );
            }
        }

        return self::$instance[$name];
    }

    /**
     * Build a DataSource Name.
     *
     * This method is used by the factory method to build
     * a datasource name for PDO based on the db_engine
     * specified in the configuration (configurable via the admin)
     *
     * @throw  Frapi_Error
     * @return string      The Datasource for the selected database engine.
     */
    public static function buildDsn(array $configs)
    {
        $dsn = false;

        if (!isset($configs['db_engine'])) {
            throw new Frapi_Error(
                 'NO_DATABASE_DEFINED',
                 'No Database is defined in the configuration',
                 500,
                 'Internal Server Error'
            );
        }

        switch ($configs['db_engine']) {
            case 'mysql':
                $dsn = 'mysql:dbname=' . $configs['db_database'] .
                       ';host='.$configs['db_hostname'];
                break;
            case 'pgsql':
                $dsn = 'pgsql:host=' . $configs['db_hostname'] .
                       ';dbname=' . $configs['db_database'] .
                       ';user=' . $configs['db_username'] .
                       ';password=' . $configs['db_password'];
                break;
            case 'mssql':
                $dsn = 'sqlsrv:Server=' . $configs['db_hostname'] .
                       ';Database=' . $configs['db_database'];
                break;
        }

        return $dsn;
    }

    /**
     * Get a database instance
     *
     * This method is used to retrieve a database instance. For the sake
     * of simplicity we are using PDO by default. Thus returning a PDO
     * object (or whichever of it's kind)
     *
     * @param  string $name The name of the instantiation (namespace emulation)
     *                      This parameter is optional and is defaulted to 'default'
     *
     * @return mixed PDO* An object of type PDO
     */
    public static function getInstance($name = 'default')
    {
        return self::factory($name);
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
