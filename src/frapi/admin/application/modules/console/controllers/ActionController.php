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
    private $yes_no_values = array('y', 'n');
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

        fwrite(
            STDOUT,
            str_pad('Name', $action_name_max_length)
            . ' Enabled Public '
            . str_pad('Route', $action_route_max_length) . PHP_EOL
        );

        foreach ($actions as $key => $action) {
            fwrite(
                STDOUT,
                str_pad($action['name'], $action_name_max_length)
                . ' ' . str_pad($action['enabled'], strlen('Enabled'))
                . ' ' . str_pad($action['public'], strlen('Public'))
                . str_pad($action['route'], $action_route_max_length) . PHP_EOL
            );
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
        $action_name = '';
        while ($action_name == '') {
            fwrite(STDOUT, 'Action Name: ');
            $action_name = trim(fgets(STDIN));
        }

        $action_enabled = '';
        while (!in_array($action_enabled, $this->yes_no_values)) {
            fwrite(STDOUT, 'Action Enabled(y/n): ');
            $action_enabled = trim(strtolower(fgets(STDIN)));
        }
        $action_enabled = ($action_enabled == 'y' ? '1' : '0');

        $action_public = '';
        while (!in_array($action_public, $this->yes_no_values)) {
            fwrite(STDOUT, 'Action Public(y/n): ');
            $action_public = trim(strtolower(fgets(STDIN)));
        }
        $action_public = ($action_public == 'y' ? '1' : '0');

        $action_custom_route = '' ;
        while (!in_array($action_custom_route, $this->yes_no_values)) {
            fwrite(STDOUT, 'Custom Route(y/n): ');
            $action_custom_route = trim(strtolower(fgets(STDIN)));
        }
        $action_custom_route = ($action_custom_route == 'y' ? '1' : '0');

        if ($action_custom_route == 1) {
            $action_custom_route_route = '';
            while ($action_custom_route_route == '') {
                fwrite(STDOUT, 'Custom Route: ');
                $action_custom_route_route = trim(strtolower(fgets(STDIN)));
            }
        }

        $action_description = '';
        while ($action_description == '') {
            fwrite(STDOUT, 'Description: ');
            $action_description = trim(fgets(STDIN));
        }

        $add_parameters = '';
        while (!in_array($add_parameters, $this->yes_no_values)) {
            fwrite(STDOUT, 'Add parameters(y/n): ');
            $add_parameters = trim(strtolower(fgets(STDIN)));
        }

        $action_parameters = array();
        if ($add_parameters == 'y') {

            while ($add_parameters == 'y') {

                $parameter_name = '';
                while ($parameter_name == '') {
                    fwrite(STDOUT, 'Parameter Name: ');
                    $parameter_name = trim(fgets(STDIN));
                }

                $parameter_required = '';
                while (!in_array($parameter_required, $this->yes_no_values)) {
                    fwrite(STDOUT, 'Parameter Required(y/n): ');
                    $parameter_required = trim(strtolower(fgets(STDIN)));
                }
                $parameter_required = ($parameter_required == 'y' ? 'on' : null);

                $action_parameters[] = array('parameter_name' => $parameter_name, 'parameter_required' => $parameter_required);

                $add_parameters = '';
                while (!in_array($add_parameters, $this->yes_no_values)) {
                    fwrite(STDOUT, 'Add another Parameter(y/n): ');
                    $add_parameters = trim(strtolower(fgets(STDIN)));
                }
            }
        }

        $submit_data = array (
            'name'              => $action_name,
            'enabled'           => $action_enabled,
            'public'            => $action_public,
            'use_custom_route'  => $action_custom_route,
            'route'             => $action_custom_route_route,
            'description'       => $action_description
        );

        foreach ($action_parameters as $parameter_data) {
           $submit_data['param'][]      = $parameter_data['parameter_name'];
           $submit_data['required'][]   = $parameter_data['parameter_required'];
        }

        $model = new Default_Model_Action();
        if ($model->add($submit_data)) {
            fwrite(STDOUT, 'Action added successfully.' . PHP_EOL);
        } else {
            fwrite(STDOUT, 'Error adding action.' . PHP_EOL);
        }
    }

    /**
     * Edit an action
     *
     * This is the edit action method. It allows you to edit an action.
     *
     * @return void
     */
    public function editAction()
    {
        fwrite(STDOUT, 'editAction has not been implemented yet.' . PHP_EOL);
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
        fwrite(STDOUT, 'Which action would you like to delete? ' . PHP_EOL);

        $model      = new Default_Model_Action();
        $actions    = $model->getAll();

        $max_action = 0;
        $action_to_delete = null;
        while (!in_array($action_to_delete, array_keys($actions), true)) {
            foreach ($actions as $key => $action) {
                fwrite(STDOUT, $key . ' - ' . $action['name'] . PHP_EOL);
                $max_action = $key;
            }
            fwrite(STDOUT, '(0 - ' . $max_action . '): ');
            $action_to_delete = (int)trim(fgets(STDIN));
        }

        $confirm = '';
        while (!in_array($confirm, $this->yes_no_values)) {
            fwrite(STDOUT, 'Are you sure you want to delete ' . $actions[$action_to_delete]['name'] . '(y/n):');
            $confirm = trim(strtolower(fgets(STDIN)));
        }

        if ($confirm == 'y') {
            $hash = $actions[$action_to_delete]['hash'];
            if ($hash) {
                $model->delete($hash);
                fwrite(STDOUT, $actions[$action_to_delete]['name'] . ' was deleted successfully.' . PHP_EOL);
            }
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
        fwrite(STDOUT, 'syncAction has not been implemented yet.' . PHP_EOL);
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
        fwrite(STDOUT, 'testAction has not been implemented yet.' . PHP_EOL);
    }
}
