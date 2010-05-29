<?php

class MockFrapi_Internal extends Frapi_Internal 
{
    /**
     * @var array
     */
    private static $_cache = array();
    
    public static function getCached($key) 
    {
        return self::$_cache[$key];
    }
    
    public static function setCached($key, $value) 
    {
        self::$_cache = array($key => $value);
    }

}
