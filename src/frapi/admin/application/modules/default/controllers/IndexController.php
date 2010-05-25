<?php
/**
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
 * @package   frapi-admin
 */
class IndexController extends Lupin_Controller_Base
{
    public function init()
    {
        $this->_helper->_acl->allow('admin', array('index'));
        parent::init();
    }

    public function indexAction()
    {
        $issues = array();
        $user   = get_current_user();

        $dir  = ROOT_PATH . DIRECTORY_SEPARATOR . 
            'admin'    . DIRECTORY_SEPARATOR . 
            'application'    . DIRECTORY_SEPARATOR . 
            'config'   . DIRECTORY_SEPARATOR . 'app';

        if (!is_writable($dir)) {
            $issues['config-path'] = 
                'The "<strong>'.$dir.'</strong>" directory is not writeable by the ' . 
                'current user ('.$user.'), therefore we will not be able to save API configurations: ('.
                'Actions, Errors, Partners, Configuration, etc) until the user has write access.';
        }
        
        $dir    = ROOT_PATH . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'Action';

        if (!is_writable($dir)) {
            $issues['custom-action-path'] = 
                'The "<strong>'.$dir.'</strong>" directory is not writeable by the ' . 
                'current user ('.$user.'), therefore we will not be able to synchronize ' .
                'the codebase until the user has write access.';
        }

        $this->view->issues = $issues;
        
    }
}
