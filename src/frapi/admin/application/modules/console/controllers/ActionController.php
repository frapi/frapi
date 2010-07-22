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
        fwrite(STDOUT, 'AddAction has not been implemented yet.' . PHP_EOL);
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
        fwrite(STDOUT, 'deleteAction has not been implemented yet.' . PHP_EOL);
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
