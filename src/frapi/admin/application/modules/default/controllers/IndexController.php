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
 * @package   frapi-admin
 */
class IndexController extends Lupin_Controller_Base
{
    private $tr;

    public function init($styles = array())
    {
        $this->tr = Zend_Registry::get('tr');
        $this->_helper->_acl->allow('admin', array('index'));
        parent::init($styles);
    }

    /**
     * Index action
     *
     * The main index does nothing special. it verifies whether or not the
     * setup can be ran by verifying the permissions on the custom directory
     * emits meesages when it's not.
     *
     * @return void
     */
    public function indexAction()
    {
        $issues = array();
        $user   = get_current_user();

        $dir = Zend_Registry::get('localConfigPath');

        if (!is_writable($dir)) {
            $configPathMessage = $this->tr->_('ACTION_WARNING_CONFIG');
            $issues['config-path'] = sprintf($configPathMessage, $dir, $user);
        }

        $dir    = CUSTOM_PATH . DIRECTORY_SEPARATOR . 'Action';

        if (!is_writable($dir)) {
            $actionPathMessage = $this->tr->_('ACTION_WARNING_ACTION');
            $issues['custom-action-path'] = sprintf($actionPathMessage, $dir, $user);
        }

        $file    = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR
                . 'js' . DIRECTORY_SEPARATOR
                . 'ace' . DIRECTORY_SEPARATOR
                . 'build' . DIRECTORY_SEPARATOR
                . 'src' . DIRECTORY_SEPARATOR
                . 'ace.js';

        if (!file_exists($file)) {
            $submoduleMessage = $this->tr->_('SUBMODULES_NOT_INITED');
            $issues['missing-ace-editor'] = $submoduleMessage;
        }

        $this->view->issues = $issues;

    }
}
