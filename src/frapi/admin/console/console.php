#!/usr/bin/php -q
<?php
/*
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
 * @license     New BSD
 * @copyright   echolibre ltd.
 * @package     frapi-admin
 * @subpackage  console
 */

// Define path to application directory
define('ROOT_PATH',        dirname(dirname(dirname(__FILE__))));
define('APPLICATION_PATH', ROOT_PATH . DIRECTORY_SEPARATOR .
                           'admin' . DIRECTORY_SEPARATOR.'application');

// Define application environment
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


set_include_path('.' . PATH_SEPARATOR . ROOT_PATH . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR . get_include_path());

// Create application, bootstrap, and run
require_once 'Zend/Application.php';

require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'library' .
DIRECTORY_SEPARATOR . 'Frapi' . DIRECTORY_SEPARATOR . 'AllFiles.php';

require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'custom'. DIRECTORY_SEPARATOR . 'AllFiles.php';

// Include our action controller here
require APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'console'. DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'ActionController.php';

$app = new Zend_Application(
    APPLICATION_ENV, APPLICATION_PATH . DIRECTORY_SEPARATOR . 'config' .
    DIRECTORY_SEPARATOR.'application.ini'
);
$app->bootstrap(array('config', 'db', 'defaultAutoloader', 'acl'));

// Valid routes we will handle
$routes = array (
    'list-actions'  => 'ActionController::listAction',
    'add-action'    => 'ActionController::addAction',
    'delete-action' => 'ActionController::deleteAction',
    'edit-action'   => 'ActionController::editAction',
    'test-action'   => 'ActionController::testAction',
    'sync-actions'  => 'ActionController::syncAction',
    'exit'          => '',
);
ini_set('display_errors', 'On');
$command = '';
while (true) {

    // What would you like to do today?
    fwrite(STDOUT, "frapi> ");
    $command = trim(fgets(STDIN));

    if (!in_array($command, array_keys($routes))) {
        fwrite(STDOUT, 'Valid commands are: ' . PHP_EOL);

        foreach ($routes as $route_command => $action) {
            fwrite(STDOUT, $route_command . PHP_EOL);
        }
        continue;
    }

    if ($command == 'exit') {
        fwrite(STDOUT, 'Goodbye' . PHP_EOL);
        exit(0);
    }

    list($controller, $method) = explode('::', $routes[$command]);

    $controller = new $controller();
    $controller->$method();
}
