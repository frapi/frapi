<?php

/*
 * Copyright 2011 Scott Morken <scott.morken@pcmail.maricopa.edu>
 * Phoenix College
 */

/**
 * Frapi_FileHelper Jun 6, 2011
 * Project: frapi
 *
 * @author Scott Morken <scott.morken@pcmail.maricopa.edu>
 */
class Frapi_FileHelper
{
    protected static $dir;

    public static function normalizeName($name, $join = '/')
    {
        if (substr($name, 0, 1) == '/') {
            $name = substr($name, 1);
        }
        $name = str_replace(' ', '_', $name);
        $expName = explode('/', $name);
        array_walk($expName, create_function('&$val', '$val = ucfirst(strtolower($val));'));
        $name = join($join, $expName);
        return $name;
    }

    public static function generateClassName($name)
    {
        return self::normalizeName($name, '_');
    }

    public static function getBaseDir()
    {
        if (!self::$dir) {
            self::$dir  = ROOT_PATH . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'Action';
        }
        return self::$dir;
    }

}
