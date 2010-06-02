<?php

class MockFrapi_Action extends Frapi_Action
{
    /**
     * Get an instance of the desired type of Action
     * Context using the action passed to it
     *
     * @param  string $action The action context to load
     * @return Action instance
     */
    public static function getInstance($action)
    {
        $directory = realpath('./unit-tests/library/Action') . '/';
        $filePlain = ucfirst(strtolower($action));
        $file      = $directory . $filePlain . '.php';
        $class     = 'Action_' . $filePlain;
        
        if (!file_exists($file) || !is_readable($file)) {
            throw new Frapi_Action_Exception (
                Frapi_Error::ERROR_INVALID_ACTION_REQUEST_MSG,
                'ERROR_INVALID_ACTION_REQUEST',
                Frapi_Error::ERROR_INVALID_ACTION_REQUEST_NO,
                null,
                400
            );
        }
        
        if (!class_exists($class, false)) {
            require $file;
        }

        return new $class;
    }

}
