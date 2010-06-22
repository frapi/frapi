<?php

class Lupin_Security
{
    /**
     * Escapes a value for output in a view script.
     *
     * If escaping mechanism is one of htmlspecialchars or htmlentities, uses
     * {@link $_encoding} setting.
     *
     * @param mixed $var The output to escape.
     * @return mixed The escaped value.
     */
    public static function escape($var)
    {
        if (isset($var) && is_string($var) && strlen(trim($var)) > 0) {
            return htmlspecialchars(stripslashes($var));
        }
        
        return '';
    }
}
