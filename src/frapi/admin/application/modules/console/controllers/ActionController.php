<?php
/*
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
class ActionController
{

    /**
     *  List action
     *
     * This is the list action. It will list all available actions.
     *
     * @return void
     */
    public function listAction()
    {
        $model      = new Default_Model_Action();
        $actions    = $model->getAll();

        // Determine our max field widths so we can pad things out appropriately
        $action_name_max_length     = 0;
        $action_route_max_length   = 0;
        foreach ($actions as $key => $action) {

            if (strlen($action['name']) > $action_name_max_length) {
                $action_name_max_length = strlen($action['name']);
            }

            if (strlen($action['route']) > $action_route_max_length) {
                $action_route_max_length = strlen($action['route']);
            }
        }

        $action_name_max_length++;
        $action_route_max_length++;

        echo  str_pad('Name', $action_name_max_length) . ' Enabled Public '
            . str_pad('Route', $action_route_max_length) . PHP_EOL;
        foreach ($actions as $key => $action) {
            echo str_pad($action['name'], $action_name_max_length)
                . ' ' . str_pad($action['enabled'], strlen('Enabled'))
                . ' ' . str_pad($action['public'], strlen('Public'))
                . ' ' . str_pad($action['route'], $action_route_max_length) . PHP_EOL;
        }
    }

    /**
     * Add an action
     *
     * This is the add action method. It literally does what it say.
     * It adds an action.
     *
     *
     * @return void
     */
    public function addAction()
    {
        // The options we are accepting for adding
        $options = new Zend_Console_Getopt(
            array(
                'name|n=s'                 => 'Name of the action.',
                'enabled|e'                => 'Is the action enabled?',
                'public|p'                 => 'Is the action public?',
                'route|r=s'                => 'Custom route of the action.',
                'description|d=s'          => 'Description of the action.',
                'parameters|pa=s'          => 'List of comma-seperated optional parameters.',
                'required-parameters|rp=s' => 'List of comma-seperated required parameters.'
            )
        );

        try {
            $options->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            echo $e->getUsageMessage();
            exit();
        }

        if ($options->name == '') {
            echo $options->getUsageMessage();
            exit();
        } else if ($options->route == '') {
            echo $options->getUsageMessage();
            exit();
        }

        $action_name               = $options->name;
        $action_enabled            = $options->enabled === true ? '1' : '0';
        $action_public             = $options->public === true ? '1' : '0';
        $action_route              = $options->route;
        $action_description        = $options->description;

        $submit_data = array (
            'name'              => $action_name,
            'enabled'           => $action_enabled,
            'public'            => $action_public,
            'route'             => $action_route,
            'description'       => $action_description
        );

        // Handle parameters passed
        $action_optional_parameters = explode(',', $options->parameters);
        $i = 0;
        foreach ($action_optional_parameters as $parameter) {
            if ($parameter != '') {
                $submit_data['param'][$i] = $parameter;
                $i++;
            }
        }

        $action_required_parameters = explode(',', $options->getOption('required-parameters'));
        foreach ($action_required_parameters as $parameter) {
            if ($parameter != '') {
                $submit_data['param'][$i]    = $parameter;
                $submit_data['required'][$i] = '1';
                $i++;
            }
        }

        $model = new Default_Model_Action();
        try {
            $model->add($submit_data);
            echo 'Successfully added action: ' . $action_name . PHP_EOL;
        } catch (RuntimeException $e) {
            echo 'Error adding action: ' . $action_name . '. ' . $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Delete an action
     *
     * This is the delete action method. It allows you to delete an action.
     *
     * @return void
     */
    public function deleteAction()
    {
        // The options we are accepting for deleting
        $options = new Zend_Console_Getopt(
            array(
                'name|n=s' => 'Name of the action.',
            )
        );

        try {
            $options->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            echo $e->getUsageMessage();
            exit();
        }
        if ($options->name == '') {
            echo $options->getUsageMessage();
            exit();
        }

        $action_name = ucfirst(strtolower($options->name));

        $model       = new Default_Model_Action();
        $tempActions = $model->getList();
        $action_id   = null;
        foreach ($tempActions as $hash => $tempName) {
            if ($action_name == $tempName) {
                $action_id = $hash;
                break;
            }
        }

        if (!$action_id) {
            echo 'Could not delete action: ' . $action_name . '. Could not find match.' . PHP_EOL;
            exit();
        }

        try {
            $model->delete($action_id);
            echo 'Successfully deleted action: ' . $action_name . PHP_EOL;
        } catch (RuntimeException $e) {
            echo 'Error deleting action: ' . $action_name . '. ' . $e->getMessage() . PHP_EOL;
        }

    }

    /**
     * Sync the actions
     *
     * This is the sync actions method. It syncs actions to the files.
     *
     * @return void
     */
    public function syncAction()
    {
        $dir  = ROOT_PATH . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'Action';

        if (!is_writable($dir)) {
            echo STDOUT, 'The path : "' . $dir
                . '" is not currently writeable by this user, '
                . 'therefore we cannot synchronize the codebase' . PHP_EOL;
            exit();
        }

        $model = new Default_Model_Action();

        try {
            $model->sync();
            echo 'All actions have been synced successfully.' . PHP_EOL;
        } catch (RuntimeException $e) {
            echo 'Error synchronizing actions. ' . $e->getMessage();
        }
    }

    /**
     * Test an action
     *
     * This is the test action method. It test a specific action.
     *
     * @return void
     */
    public function testAction()
    {
        echo 'Coming soon to a FRAPI install near you!' . PHP_EOL;
    }
}
