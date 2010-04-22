<?php
/**
 * Lupin TimeoutHandler Plugin
 *
 * Expires users sessions based on their inactivity.
 */

/**
 * Handles session timeout according to configuration
 */
class Lupin_Plugin_TimeoutHandler extends Zend_Controller_Plugin_Abstract
{
    /**
     * Listens to request dispatches, captures it and use the request object to
     * make sure that the user has not been inactive too long.
     *
     * @param Zend_Controller_Request_Abstract $request The request object
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $namespace =  new Zend_Session_Namespace('inactivity');
        $expire    = Zend_Registry::get('config')->session->expire;

        if (isset($namespace->lastActivity) &&
            ((time() - $namespace->lastActivity) > $expire)) {
            Zend_Auth::getInstance()->clearIdentity();
        }
        $namespace->lastActivity = time();
    }
}