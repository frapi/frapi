<?php
/**
 * Router Class
 *
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
 * Two main ways to use:
 * 1. Manually Prepare and Set Routes. Best used when we have our routes in array, or from file.
 *    <?php
 *        $router = new Frapi_Router();
 *        $router->setPreparedRoutes(Frapi_Router::prepareRoutes($routes));
 *        $result = $router->match('/example-object/189/edit');
 *    ?>
 *
 * 2. Let Frapi_Router load and prepare routes
 *    <?php
 *        $router = new Frapi_Router();
 *        $router->loadAndPrepareRoutes(); //Load prepared routes from APC, else load from DB and prepare.
 *        $result = $router->match('/example-object/189/edit');
 *    ?>
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Frapi_Router
{
    /**
     * Prepared Routes
     *
     * @var Array
     */
    protected $preparedRoutes = array();

    /**
     * A list of the reserved routes that Frapi may make use
     * of at some given point or another.
     *
     * @var array An array of reservedRoutes
     */
    protected $reservedRoutes = array(
        '/_oauth', '/_xauth'
    );

    /**
     * Load routes from APC, database etc.
     * Prepare routes if necessary!
     *
     * @return void
     */
    public function loadAndPrepareRoutes()
    {
        if ($routes = Frapi_Internal::getCached('Router.routes-prepared')) {
            $this->setPreparedRoutes($routes);
        } else {
            $routes = array();

            $ret    = Frapi_Internal::getConfiguration('actions');
            $rows = $ret->getAll('action');

            foreach ($rows as $row) {
                if (isset($row['route']) && !empty($row['route'])) {
                    $routes[$row['name']] = $row['route'];
                }
            }

            $this->setPreparedRoutes($preparedRoutes = self::prepareRoutes($routes));
            Frapi_Internal::setCached('Router.routes-prepared', $preparedRoutes);
        }
    }

    /**
     * Prepare routes
     *
     * Turns array of routes into optimized arrays
     * which can be quickly looked up.
     *
     * @param  array $routes The routes to parse.
     * @return array $routes
     */
    public static function prepareRoutes($routes)
    {
        $optimizedRoutes = array();
        foreach ($routes as $action => $route) {
            $parsedRoute = self::parseSegments($route);
            if ($parsedRoute[0][0] != ':') {

                if (!isset($optimizedRoutes[current($parsedRoute)])) {
                    $optimizedRoutes[current($parsedRoute)] = array();
                }

                $optimizedRoutes[current($parsedRoute)][] =
                    array('segments'=>array_slice($parsedRoute, 1), 'action'=>$action);
            }
        }

        return $optimizedRoutes;
    }

    /**
     * Parse route or path in array segments.
     *
     * @param  string $route Parse the segments of a route
     * @return Array Segments.
     */
    public static function parseSegments($route)
    {
        $route    = trim($route, ' /');
        $exploded = preg_split('@[/]+@', $route);

        return $exploded;
    }

    /**
     * Given a query path, match against URL segments
     *
     * @param $queryPath The query path we want to route.
     * @return mixed Array|false array('params'=>array(), 'action'=>String)
     */
    public function match($queryPath)
    {
        if (empty($this->preparedRoutes)) {
            return false;
        }

        $matches          = array('static' => array(), 'dynamic' => array());
        $explodedPath     = self::parseSegments($queryPath);
        $firstPathSegment = current($explodedPath);

        if (isset($this->preparedRoutes[$firstPathSegment])) {
            foreach ($this->preparedRoutes[$firstPathSegment] as $route) {
                $type = "static";

                // Wildcard match. Prioritized over anything else.
                if (isset($route['segments'][count($route['segments'])-1]) && $route['segments'][count($route['segments'])-1] == '*') {
                    $rest = array_slice($explodedPath, count($route['segments']), count($explodedPath)+1);

                    $type = 'dynamic';
                    $matches[$type][] = array(
                        'params' => array('*' => implode('/', $rest)),
                        'action' => $route['action']
                    );

                    continue;
                }

                if (count($route['segments']) + 1 == count($explodedPath)) {
                    $params = array();

                    foreach (array_slice($explodedPath, 1) as $pathSegment) {
                        $routeSegment = current($route['segments']);
                        if ($routeSegment[0] == ':') {
                            $params[substr($routeSegment, 1)] = $pathSegment;
                            $type = "dynamic";
                        } elseif ($routeSegment != $pathSegment) {
                            continue 2;
                        }

                        next($route['segments']);
                    }
                    //To reach here, all segments of query must have matched
                    //so we store for later, and we'll choose static over dynamic results.
                    $matches[$type][] = array('params'=>$params, 'action'=>$route['action']);
                }
            }
        } else {
            return false;
        }

        if (!empty($matches['static'])) {
            return current($matches['static']);
        } else if (!empty($matches['dynamic'])) {
            return current($matches['dynamic']);
        }

        return false;
    }

    /**
     * Set prepared routes
     *
     * This method sets the prepared routes
     *
     * @param  array $route  An array of routes that have been prepared
     * @return void
     */
    public function setPreparedRoutes($routes)
    {
        $this->preparedRoutes = $routes;
    }
}
