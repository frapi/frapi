<?php
/**
 * AllFiles
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
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH',         dirname(dirname(dirname(__FILE__))));
}

define('CUSTOM_PATH',       ROOT_PATH . DIRECTORY_SEPARATOR . 'custom');
define('LIBRARY_ROOT_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'Frapi');
define('EXTRA_LIBRARIES_ROOT_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR);

define('LIBRARY_OUTPUT',        LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Output');
define('LIBRARY_SECURITY',      LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Security');
define('LIBRARY_ERROR',         LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Error');
define('LIBRARY_RULES',         LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Rules');
define('LIBRARY_CACHE',         LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Cache');
define('LIBRARY_AUTHORIZATION', LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Authorization');

// Auth adapter
define('LIBRARY_AUTHORIZATION_ADAPTER', LIBRARY_AUTHORIZATION . DIRECTORY_SEPARATOR . 'Adapter');

// Cache Adapter
define('LIBRARY_CACHE_ADAPTER', LIBRARY_CACHE . DIRECTORY_SEPARATOR . 'Adapter');

// Custom paths
define('CUSTOM_ACTION',   CUSTOM_PATH . DIRECTORY_SEPARATOR . 'Action');
define('CUSTOM_OUTPUT',   CUSTOM_PATH . DIRECTORY_SEPARATOR . 'Output');
define('CUSTOM_MODEL',    CUSTOM_PATH . DIRECTORY_SEPARATOR . 'Model');
define('CUSTOM_CONFIG',  CUSTOM_PATH . DIRECTORY_SEPARATOR . 'Config');
define('ADMIN_CONF_PATH', CUSTOM_CONFIG . DIRECTORY_SEPARATOR);

define('API_MODELS',     ROOT_PATH . DIRECTORY_SEPARATOR . 'Models');

// Exception
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Exception.php';

// Internal (DB access to admin SQLite)
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Internal.php';

// Logger
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Logger.php';

// API Controller
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . 'Main.php';
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . 'Api.php';

//Routing
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Router.php';

// Action
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Action' . DIRECTORY_SEPARATOR . 'Exception.php';
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Action' . DIRECTORY_SEPARATOR . 'Interface.php';
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Action.php';

// Input
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Input' . DIRECTORY_SEPARATOR . 'RequestBodyParser.php';
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Input' . DIRECTORY_SEPARATOR . 'XmlParser.php';

// Response
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Response.php';
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Response/Custom.php';

//Database
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Database.php';

// Output
require LIBRARY_OUTPUT    . DIRECTORY_SEPARATOR . 'Interface.php';
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Output.php';
require LIBRARY_OUTPUT    . DIRECTORY_SEPARATOR . 'JSON.php';
require LIBRARY_OUTPUT    . DIRECTORY_SEPARATOR . 'XML.php';
require LIBRARY_OUTPUT    . DIRECTORY_SEPARATOR . 'HTML.php';
require LIBRARY_OUTPUT    . DIRECTORY_SEPARATOR . 'CLI.php';
require LIBRARY_OUTPUT    . DIRECTORY_SEPARATOR . 'PHP.php';
require LIBRARY_OUTPUT    . DIRECTORY_SEPARATOR . 'PRINTR.php';
require LIBRARY_OUTPUT    . DIRECTORY_SEPARATOR . 'JS.php';
require LIBRARY_OUTPUT    . DIRECTORY_SEPARATOR . 'Custom.php';

// Security
require LIBRARY_SECURITY  . DIRECTORY_SEPARATOR . 'Interface.php';
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Security.php';

// Error
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Error.php';

// Rules
require LIBRARY_RULES     . DIRECTORY_SEPARATOR . 'Interface.php';
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Rules.php';

// Cache
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Cache.php';
require LIBRARY_CACHE     . DIRECTORY_SEPARATOR . 'Interface.php';
require LIBRARY_CACHE     . DIRECTORY_SEPARATOR . 'Exception.php';
require LIBRARY_CACHE     . DIRECTORY_SEPARATOR . 'Adapter' . DIRECTORY_SEPARATOR . 'Exception.php';

// Authorization
require LIBRARY_AUTHORIZATION . DIRECTORY_SEPARATOR . 'Interface.php';
require LIBRARY_ROOT_PATH     . DIRECTORY_SEPARATOR . 'Authorization.php';
require LIBRARY_AUTHORIZATION . DIRECTORY_SEPARATOR . 'Partner.php';

// Xml Adapter
require LIBRARY_AUTHORIZATION_ADAPTER . DIRECTORY_SEPARATOR . 'Xml.php';

// Models
require LIBRARY_ROOT_PATH . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'Partner.php';

// HTTP Digest Auth
require LIBRARY_AUTHORIZATION . DIRECTORY_SEPARATOR . 'HTTP' . DIRECTORY_SEPARATOR . 'Digest.php';

// Lupin XML config stuff.
require EXTRA_LIBRARIES_ROOT_PATH . 'Lupin' . DIRECTORY_SEPARATOR .
                                    'Config' . DIRECTORY_SEPARATOR . 'Xml.php';

require EXTRA_LIBRARIES_ROOT_PATH . 'Lupin' . DIRECTORY_SEPARATOR  .
                                    'Config' . DIRECTORY_SEPARATOR .
                                    'Helper' . DIRECTORY_SEPARATOR . 'XmlArray.php';
// Require the CUSTOM AllFiles
require CUSTOM_PATH . DIRECTORY_SEPARATOR . 'AllFiles.php';


