<?php
/**
 * Authorization
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
class Frapi_Authorization
{
    /**
     * The action currently performed and set as it is
     * in the controller
     */
    protected $action;

    /**
     * This is the parameters passed to the
     * application.
     *
     * @var array $params  The parameters passed to the context
     */
    protected $params;

    /**
     * This contains the login and the partner objects.
     *
     * @var object $partner  AuthorizationPartner
     */
    protected $partner;

    /**
     * This method sets the parameters passed to the context
     *
     * @param  array The parameters to use in contexts.
     * @return Object $this
     */
    public function setAuthorizationParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * This returns an object of the partner context
     *
     * @return AuthorizationPartner
     */
    public function getPartner()
    {
        $this->partner = new Frapi_Authorization_Partner();
        return $this->partner;
    }

    /**
     * This method will set the action we are using
     * and will make it usuable by it's childs.
     *
     * @param   string $action  The action
     * @return  $this;
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * This method will get the action
     * and return it's value.
     *
     * @return string  $this->action
     */
    public function getAction()
    {
        return $this->action;
    }
}
