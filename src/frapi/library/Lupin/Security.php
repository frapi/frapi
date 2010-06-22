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
        if (is_array($var) && empty($var)) {
            return false;
        }

        return htmlspecialchars(stripslashes($var));
    }
}
