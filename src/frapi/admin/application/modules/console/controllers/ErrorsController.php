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
class ErrorsController
{
    /*
     *  List action
     *
     * This is the list action. It will list all available actions.
     *
     * @return void
     */
    public function listAction()
    {
        $model  = new Default_Model_Error();
        $errors = $model->getAll();

        // Determine our max field widths so we can pad things out appropriately
        $error_name_max_length = strlen('Name');

        if ($errors) {
            foreach ($errors as $key => $error) {

                if (strlen($error['name']) > $error_name_max_length) {
                    $error_name_max_length = strlen($error['name']) + 1;
                }
            }
        }

        echo  str_pad('Name', $error_name_max_length) . 'HTTP Code' . PHP_EOL;

        if ($errors) {
            foreach ($errors as $key => $error) {
                echo str_pad($error['name'], $error_name_max_length). $error['http_code'] . PHP_EOL;
            }
        }
    }

    /**
     * Add an error
     *
     * This is the add error method. It literally does what it say.
     * It adds an error.
     *
     *
     * @return void
     */
    public function addAction()
    {
        $options = new Zend_Console_Getopt(
            array (
                'name|n=s' => 'Name of the error.',
                'code|c-i' => 'HTTP code of error.',
                'message|m=s' => 'Error message',
                'description|d-s' => 'Description fo the error.'
            )
        );

        try {
            $options->parse();
        } catch (Exception $e) {
            echo $e->getUsageMessage();
            exit();
        }

        if ($options->name == '') {
            echo $options->getUsageMessage();
            exit();
        } else if ($options->message == '') {
            echo $options->getUsageMessage();
            exit();
        }

        $error_name        = $options->name;
        $error_code        = $options->code;
        $error_message     = $options->message;
        $error_description = $options->description;

        $submit_data = array(
            'name'          => $error_name,
            'http_code'     => $error_code,
            'message'       => $error_message,
            'description'   => $error_description,
        );

        $model = new Default_Model_Error();

        try {
            $model->add($submit_data);
            echo 'Successfully added error: ' . $error_name . PHP_EOL;
        } catch (RuntimeException $e) {
            echo 'Error adding error: ' . $error_name . '. ' . $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Delete an error
     *
     * This is the delete error method. It allows you to delete an error.
     *
     * @return void
     */
    public function deleteAction()
    {
        // The options we are accepting for deleting
        $options = new Zend_Console_Getopt(
            array(
                'name|n=s' => 'Name of the error.',
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

        $error_name = strtoupper($options->name);
        $model      = new Default_Model_Error();
        $tempErrors = $model->getAll();
        $error_id   = null;
        foreach ($tempErrors as $key => $value) {
            if ($error_name == strtoupper($value['name'])) {
                $error_id = $value['hash'];
                break;
            }
        }

        if (!$error_id) {
            echo 'Could not delete error: ' . $error_name . '. Could not find match.' . PHP_EOL;
            exit();
        }

        try {
            $model->delete($error_id);
            echo 'Successfully deleted error: ' . $error_name . PHP_EOL;
        } catch (RuntimeException $e) {
            echo 'Error deleting errror: ' . $error_name . '. ' . $e->getMessage() . PHP_EOL;
        }
    }
}
