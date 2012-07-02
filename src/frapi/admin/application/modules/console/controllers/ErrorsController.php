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
 * @package   frapi-admin
 */
class Console_ErrorsController extends Zend_Controller_Action
{

    private $tr;

    /**
     * Main Initializer
     *
     * This is the public method that will be used by the controller base
     */
    public function init()
    {
        $this->tr = Zend_Registry::get('tr');
        $this->view->tr = Zend_Registry::get('tr');
    }

    /*
     *  List action
     *
     * This is the list action. It will list all available actions.
     *
     * @return void
     */
    public function listAction()
    {
        $this->_helper->viewRenderer->setViewSuffix('txt');

        $model              = new Default_Model_Error();
        $this->view->errors = $model->getAll();
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
        $this->_helper->viewRenderer->setViewSuffix('txt');

        // The options we allow for adding
        $options = new Zend_Console_Getopt(
            array (
                'name|n=s'        => $this->tr->_('NAME'),
                'code|c-i'        => $this->tr->_('HTTP_CODE'),
                'message|m=s'     => $this->tr->_('ERROR_MESSAGE'),
                'description|d-s' => $this->tr->_('DESCRIPTION'),
                'httpphrase|h-s'  => $this->tr->_('HTTP_REASON_PHRASE'),
            )
        );

        try {
            $options->parse();
        } catch (Exception $e) {
            $this->view->message =  $e->getUsageMessage();
            return;
        }

        if ($options->name == '') {
            $this->view->message = $options->getUsageMessage();
            return;
        } else if ($options->message == '') {
            $this->view->message = $options->getUsageMessage();
            return;
        }

        $error_name        = $options->name;
        $error_code        = $options->code;
        $error_message     = $options->message;
        $error_description = $options->description;
        $http_phrase       = $options->httpphrase;

        $submit_data = array(
            'name'          => $error_name,
            'http_code'     => $error_code,
            'message'       => $error_message,
            'description'   => $error_description,
            'http_phrase'   => $http_phrase,
        );

        $model = new Default_Model_Error();

        try {
            $model->add($submit_data);
            $this->view->message = $this->tr->_('ADDED_ERROR') . ': ' . $error_name . PHP_EOL;
        } catch (RuntimeException $e) {
            $this->view->message = $this->tr->_('ERROR_ADDING_ERROR') . ': ' . $error_name . '. ' . $e->getMessage() . PHP_EOL;
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
        $this->_helper->viewRenderer->setViewSuffix('txt');

        // The options we are accepting for deleting
        $options = new Zend_Console_Getopt(
            array(
                'name|n=s' => $this->tr->_('NAME'),
            )
        );

        try {
            $options->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            $this->view->message = $e->getUsageMessage();
            return;
        }
        if ($options->name == '') {
            $this->view->message = $options->getUsageMessage();
            return;
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
            $this->view->message = $this->tr->_('COULD_NOT_DELETE_ERROR') . ': ' . $error_name . '. ' . $this->tr->_('COULD_NOT_FIND_MATCH') . '.' . PHP_EOL;
            return;
        }

        try {
            $model->delete($error_id);
            $this->view->message = $this->tr->_('SUCCESS_DELETE_ERROR') . ': ' . $error_name . PHP_EOL;
        } catch (RuntimeException $e) {
            $this->view->message = $this->tr->_('ERROR_DELETING_ERROR') . ': ' . $error_name . '. ' . $e->getMessage() . PHP_EOL;
        }
    }
}
