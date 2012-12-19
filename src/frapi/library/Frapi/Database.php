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
    protected static function factory($dbname)
    {
        if (!isset(self::$instance[$dbname])) {
            $dbconfig = self::getDbConfig($dbname);

            $dsn = self::buildDsn($dbconfig);

            // I iz not happy with this. We already have a switch
            // for the dsn in the "buildDsn" method...
            if (isset($dbconfig['engine']) &&
                in_array($dbconfig['engine'], array('pgsql')))
            {
                // DSN that have the user/pass implicitely defined.
                self::$instance[$dbname] = new PDO($dsn);
            } else {
                // Other dsns like mysql, mssql, etc.
                self::$instance[$dbname] = new PDO(
                    $dsn,
                    $dbconfig['username'],
                    $dbconfig['password'],
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\'')
                );
            }
        }

        return self::$instance[$dbname];
    }

    /**
     * Retrieve the cached configuration parameter
     *
     * This method retrieves the cached parameter. If the caching method
     * does not identify anything from the cache then we parse the XML file.
     *
     * @param string $key The key of the cached parameter to fetch.
     * @return string The parameter value.
     */
    public static function getDbConfig($dbname)
    {
        if ($cached = Frapi_Internal::getCached('Databases.'.$dbname)) {
            return $cached;
        } else {

            $conf  = Frapi_Internal::getConfiguration('databases');
            $databases = $conf->getAll('database');

            if ($databases !== false) {
                foreach ($databases as $database) {
                    if($dbname == $database['dbname']){
                    	$dbConf = array(
                        	'hostname'  => $database['hostname'],
                        	'port'      => $database['port'],
                        	'username'  => $database['username'],
                        	'password'  => $database['password'],
                        	'engine'    => $database['engine'],
                        	'dbname'    => $database['dbname'],
                    	);
            			Frapi_Internal::setCached('Databases.'.$dbname, $dbConf);       	
            		}
                }
            }
            return Frapi_Internal::getCached('Databases.'.$dbname);
        }
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
    public static function buildDsn(array $dbconfig)
    {
        $dsn = false;

        if (!isset($dbconfig['engine'])) {
            throw new Frapi_Error(
                 'NO_DATABASE_DEFINED',
                 'No Database is defined in the configuration',
                 500,
                 'Internal Server Error'
            );
        }

        switch ($dbconfig['engine']) {
            case 'mysql':
                $dsn = 'mysql:dbname=' . $dbconfig['dbname'] .
                       ';host='.$dbconfig['hostname'].';port='.$dbconfig['port'];
                break;
            case 'pgsql':
                $dsn = 'pgsql:host=' . $dbconfig['hostname'] .
                       ';dbname=' . $dbconfig['dbname'] .
                       ';user=' . $dbconfig['username'] .
                       ';password=' . $dbconfig['password'].';port='.$dbconfig['port'];
                break;
            case 'mssql':
                $dsn = 'sqlsrv:Server=' . $dbconfig['hostname'] .
                       ';Database=' . $dbconfig['dbname'];//TODO : check how to use port  
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
    public static function getInstance($dbname)
    {
        return self::factory($dbname);
    }

}
