<?php
/**
 */
class Lupin_Acl extends Zend_Acl
{
    public function __construct()
    {
        $config = Zend_Registry::get('config');
        $this->_addRoles($config->acl->roles);
    }

    protected function _addRoles($roles)
    {
        foreach ($roles as $name => $parents) {
            if (!$this->hasRole($name)) {
                $parents = empty($parents) ? null : explode(',', $parents);
                $this->addRole(new Zend_Acl_Role($name), $parents);
            }
        }
    }
}