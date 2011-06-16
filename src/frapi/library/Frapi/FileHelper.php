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
    /**
     * base action directory
     * NOT USED
     * @var string
     */
    protected static $dir;

    /**
     * creates a normalized name from the Action name passed in
     * with the default join of '/', returns a name matching the file name
     * with the join of '_', returns the class name
     * @param string $name
     * @param string $join
     * @return string
     */
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

    /**
     * calls ::normalizeName with a join character of '_' to return the class name
     * @param string $name
     * @return string
     */
    public static function generateClassName($name)
    {
        return self::normalizeName($name, '_');
    }

    /**
     * creates the base directory for Action
     * NOT USED CURRENTLY
     * @return string
     */
    public static function getBaseDir()
    {
        if (!self::$dir) {
            self::$dir  = ROOT_PATH . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'Action';
        }
        return self::$dir;
    }

}
