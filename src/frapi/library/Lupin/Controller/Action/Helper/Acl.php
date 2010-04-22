<?php

class Lupin_Controller_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Zend_Controller_Action
     */
    protected $_action;

    /**
     * @var Zend_Auth
     */
    protected $_auth;

    /**
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * @var string
     */
    protected $_controllerName;

    /**
     * Constructor
     *
     * Optionally set view object and options.
     *
     * @param  Zend_View_Interface $view
     * @param  array $options
     * @return void
     */
    public function __construct(Zend_View_Interface $view = null, array $options = array())
    {
        $this->_auth = Zend_Auth::getInstance();
        $this->_acl = $options['acl'];
    }

    /**
     * Hook into action controller initialization
     *
     * @return void
     */
    public function init()
    {
        $this->_action = $this->getActionController();

        // add resource for this controller
        $controller = $this->_action->getRequest()->getControllerName();
        if (!$this->_acl->has($controller)) {
            $this->_acl->add(new Zend_Acl_Resource($controller));
        }

    }

    /**
     * Hook into action controller preDispatch() workflow
     *
     * @return void
     */
    public function preDispatch()
    {
        $role = Zend_Registry::get('config')->acl->defaultRole;
        if ($this->_auth->hasIdentity()) {
            $user = $this->_auth->getIdentity();
            if (is_object($user) && !empty($user->role)) {
                $role = $user->role;
            }
        }

        $request    = $this->_action->getRequest();
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        $module     = $request->getModuleName();
        $this->_controllerName = $controller;

        $resource  = $controller;
        $privilege = $action;

        if (!$this->_acl->has($resource)) {
            $resource = null;
        }

        if ($resource == 'error' && $privilege == 'error') {
            return;
        }

        if (!$this->_acl->isAllowed($role, $resource, $privilege)) {
            $request->setModuleName('default')->setControllerName('auth')->setActionName('noaccess');
            $request->setDispatched(false);
            return;
        }
    }

    /**
     * Proxy to the underlying Zend_Acl's allow()
     *
     * We use the controller's name as the resource and the
     * action name(s) as the privilege(s)
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  string|array                             $actions
     * @uses   Zend_Acl::setRule()
     * @return Expenses_Controller_Action_Helper_Acl Provides a fluent interface
     */
    public function allow($roles = null, $actions = null)
    {
        $resource = $this->_action->getRequest()->getControllerName();
        $this->_acl->allow($roles, $resource, $actions);
        return $this;
    }

    /**
     * Proxy to the underlying Zend_Acl's deny()
     *
     * We use the controller's name as the resource and the
     * action name(s) as the privilege(s)
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  string|array                             $actions
     * @uses   Zend_Acl::setRule()
     * @return Expenses_Controller_Action_Helper_Acl Provides a fluent interface
     */
    public function deny($roles = null, $actions = null)
    {
        $resource = $this->_action->getRequest()->getControllerName();
        $this->_acl->deny($roles, $resource, $actions);
        return $this;
    }

    public function isAllowed()
    {
        var_dump($this->_action->getRequest());
    }
}
