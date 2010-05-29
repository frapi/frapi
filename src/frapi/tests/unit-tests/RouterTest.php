<?php

require_once 'phpunit/Framework.php';
require '../library/Frapi/Router.php';

class RouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that router is prepating routes correctly!
     *
     * @dataProvider routesProvider
     **/
    public function testPrepareRoute($action, $route, $preparedRoute)
    {
        $this->assertEquals(
            $preparedRoute, 
            Frapi_Router::prepareRoutes(array($action=>$route))
        );
    }
    
    /**
     * Test that paths match their expected routes.
     *
     * @dataProvider pathsProvider
     **/
    public function testMatchPaths($path, $matchResult)
    {
        $router = new Frapi_Router();
        $routesToPrepare = array();
        foreach ($this->routesProvider() as $route) {
            $routesToPrepare[$route[0]] = $route[1];
        }
        $router->setPreparedRoutes(Frapi_Router::prepareRoutes($routesToPrepare));
        
        $this->assertEquals(
            $matchResult, 
            $router->match($path)
        );
    }
    
    /**
     * /user/login should match /user/login before /user/:id or /user/:name
     **/
    public function testStaticOverDynamic()
    {
        $router = new Frapi_Router();
        $routesToPrepare = array(
            "user-id-login" => "/user/:id/login",
            "user"          => "/user/",
            "user-id"       => "/user/:id",
            "user-login"    => "/user/login",
            "user-logout"   => "/user/logout",
            "container-get" => "/container/get/:id",
            "container-get-all" => "/container/get/all"
        );
        $router->setPreparedRoutes(Frapi_Router::prepareRoutes($routesToPrepare));
        
        $this->assertEquals(
            "user", 
            end($router->match("/user"))
        );
        
        $this->assertEquals(
            "user-id", 
            end($router->match("/user/1234"))
        );
        
        $this->assertEquals(
            "user-login", 
            end($router->match("/user/login/"))
        );
        
        $this->assertEquals(
            "user-logout", 
            end($router->match("/user/logout"))
        );
        
        $this->assertEquals(
            "container-get-all", 
            end($router->match("/container/get/all"))
        );
        
        $this->assertEquals(
            "container-get", 
            end($router->match("/container/get/12345"))
        );
    }
    
    /**
     * Test segments of route or path are parsed correctly
     * @dataProvider segmentsProvider
     **/
    public function testSegmentsParser($uri, $expectedSegments)
    {
        $this->assertEquals($expectedSegments, Frapi_Router::parseSegments($uri));
    }
    
    /**
     * Segments Provider
     **/
    public function segmentsProvider()
    {
        return array(
            array('/route/to/something/:wild/else', array('route', 'to', 'something', ':wild', 'else')),
            array(':wild/:wildagain/:wildyetagain', array(':wild', ':wildagain', ':wildyetagain')),
            array('/slash-beginning/middle/slash-ending/', array('slash-beginning', 'middle', 'slash-ending')),
            //Segment parser shall consume multiple slashes, if typed in error.
            array('/slash-beginning//middle/slash-ending/', array('slash-beginning', 'middle', 'slash-ending'))
            );
    }
    
    /**
     * Routes Provider
     */
     public function routesProvider()
     {
         //In format array($action, $route, $preparedRoute)
         return array(
             //Route, with one :wildcard
             array(
                 'oneWildcard', 
                 '/route/to/one/:wildcard', 
                 array('route'=>array(0=>array('segments'=>array('to', 'one', ':wildcard'), 'action'=>'oneWildcard')))
             ),
             //Route, with two :wildcard
             array(
                 'twoWildcard', 
                 '/route/to/:wildcard1/:wildcard2', 
                 array('route'=>array(0=>array('segments'=>array('to', ':wildcard1', ':wildcard2'), 'action'=>'twoWildcard')))
             ),
             //Slashes at beginning and end should be ignored
             array(
                 'endingWithSlashes', 
                 '/route/to/:wildcard1/', 
                 array('route'=>array(0=>array('segments'=>array('to', ':wildcard1'), 'action'=>'endingWithSlashes')))
             ),
             //Missing slash at beginning shouldn't matter
             array(
                 'noBeginningSlash', 
                 'route/to/:wildcard1/', 
                 array('route'=>array(0=>array('segments'=>array('to', ':wildcard1'), 'action'=>'noBeginningSlash')))
             )
             ,
             //First part of route is wildcard
             //Router should not allow these routes - WILL NOT be added to prepared routes array.
             array(
                 'firstSegmentWildcard', 
                 ':wildcard/something/else', 
                 array()
            )
        );
     }
     
     /**
      * Paths Provider
      */
      public function pathsProvider()
      {
          return array(
              //Simple wildcard match
              array(
                  'route/to/one/8z3mn8m238dh',
                  array('params'=>array('wildcard'=>'8z3mn8m238dh'), 'action'=>'oneWildcard') 
                  ),
              //Simple two-wildcard match
              array(
                  'route/to/8z3mn8m238dh/38u9d8238djo',
                  array('params'=>array('wildcard1'=>'8z3mn8m238dh', 'wildcard2'=>'38u9d8238djo'), 'action'=>'twoWildcard') 
                  ),
              //Slashes are irrelevant
              //e.g. path "seg" should match "/seg/"
              array(
                  'route/to/2384937898',
                  array('params'=>array('wildcard1'=>'2384937898'), 'action'=>'endingWithSlashes') 
                  ),
              //Wildcard value given as first segment, this is not allowed.
              array(
                  '2384937898/',
                  false
                  ),
              //Path with format suffix
              //The .json is included in the params because it is not Router's job to recognise suffixes
              array(
                  '/route/to/somerandomwildcardstuff.json',
                  array('params'=>array('wildcard1'=>'somerandomwildcardstuff.json'), 'action'=>'endingWithSlashes') 
                  )
              );
      }
}