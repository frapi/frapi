<?php
date_default_timezone_set('Europe/London');

// Define path to application directory
define('ROOT_PATH',        dirname(dirname(__FILE__)));
define('APPLICATION_PATH', ROOT_PATH . '/application');

// Define application environment
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

set_include_path('.' . PATH_SEPARATOR . ROOT_PATH . DIRECTORY_SEPARATOR . 'library');

// Autoload setups
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->registerNamespace('echolibre')->registerNamespace('Frapi');


// Make sure we have write permission on the dir the db will be in
chmod(ROOT_PATH, 0777); 

$configPath = dirname(__FILE__).'/application/config/';
Zend_Registry::set('configPath', $configPath);

$config = new Zend_Config_Ini($configPath . 'application.ini', 'development');

$dsn = $config->resources->db->params->toArray();
$dsn['dbname'] = str_replace('../', '', $dsn['dbname']);
$db = Zend_Db::factory($config->resources->db->adapter, $dsn);

$sql = '
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    handle TEXT(20) NOT NULL,
    password TEXT(40) NOT NULL,
    role TEXT(40) NOT NULL DEFAULT "normal",
    active TINYINT(1) NOT NULL DEFAULT "1"
);';
$db->query($sql);

$sql = 'INSERT INTO users (handle, password, role, active) VALUES ("admin", "' . sha1('password') . '", "admin", "1")';
$db->query($sql);

$sql = '
CREATE TABLE actions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT(50) NOT NULL,
    enabled BOOL NOT NULL,
    public BOOL NOT NULL,
    custom_route TEXT(100) NULL,
    description TEXT(250) NOT NULL DEFAULT ""
);';
$db->query($sql);

$sql = '
CREATE TABLE actions_params (
    id  INTEGER PRIMARY KEY AUTOINCREMENT,
    action_id INTEGER NOT NULL,
    name TEXT(30) NOT NULL,
    required BOOL NOT NULL DEFAULT 0
);';
$db->query($sql);

$sql = '
CREATE TABLE errors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    http_code TEXT(5) NULL,
    name TEXT(40) NOT NULL,
    msg TEXT NOT NULL,
    description TEXT(250) NOT NULL DEFAULT ""
);';
$db->query($sql);

$sql = '
CREATE TABLE actions_errors (
    error_id INTEGER NOT NULL,
    action_id INTEGER NOT NULL,
    PRIMARY KEY (error_id, action_id)
);';
$db->query($sql);

$sql = '
CREATE TABLE output (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT(30) NOT NULL,
    enabled BOOL NOT NULL DEFAULT 1,
    default_output BOOL NOT NULL DEFAULT 0
);';
$db->query($sql);

$sql = '
CREATE TABLE partners (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    firstname TEXT(60) NOT NULL,
    lastname TEXT(80) NOT NULL,
    company TEXT(100) NOT NULL,
    email text(255) NOT NULL,
    api_key text(40) NOT NULL,
    UNIQUE(api_key)
);';
$db->query($sql);

$sql = '
CREATE TABLE configurations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    value TEXT(100) NOT NULL,
    key TEXT(100) NOT NULL
);';
$db->query($sql);

chmod($dsn['dbname'], 0777);