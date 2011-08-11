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
class Console_PartnerController extends Zend_Controller_Action
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

    /**
     *  List action
     *
     * This is the list action. It will list all available partners.
     *
     * @return void
     */
    public function listAction()
    {
        $this->_helper->viewRenderer->setViewSuffix('txt');

        $model           = new Default_Model_Partner();
        $this->view
              ->partners = $model->getAll();
    }

    /**
     * Add a partner
     *
     * This is the add partner method. It literally does what it say.
     * It adds a partner.
     *
     * @return void
     */
    public function addAction()
    {
        $this->_helper->viewRenderer->setViewSuffix('txt');

        // The options we are accepting for adding
        $options = new Zend_Console_Getopt(
            array(
                'first-name|fn=s' => $this->tr->_('FIRSTNAME'),
                'last-name|ln=s'  => $this->tr->_('LASTNAME'),
                'email|e=s'       => $this->tr->_('EMAIL_USERNAME'),
                'company|c=s'     => $this->tr->_('COMPANY'),
            )
        );

        try {
            $options->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            $this->view->message = $e->getUsageMessage();
            $this->render();
            return;
        }

        if ($options->getOption('first-name') == ''
            || $options->getOption('last-name') == ''
            || $options->email == ''
            || $options->company == '') {
            $this->view->message = $options->getUsageMessage();
            return;
        }

        $partner_first_name = $options->getOption('first-name');
        $partner_last_name  = $options->getOption('last-name');
        $partner_email      = $options->email;
        $partner_company    = $options->company;

        $submit_data = array (
            'firstname' => $partner_first_name,
            'lastname'  => $partner_last_name,
            'email'     => $partner_email,
            'company'   => $partner_company,
        );

        $model = new Default_Model_Partner();
        try {
            $model->add($submit_data);
            $this->view->message = 'Successfully added partner: ' . $partner_email . PHP_EOL;
        } catch (RuntimeException $e) {
            $this->view->message = 'Error adding partner: ' . $partner_email . '. ' . $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Delete a partner
     *
     * This is the delete action method. It allows you to delete a partner.
     *
     * @return void
     */
    public function deleteAction()
    {
        $this->_helper->viewRenderer->setViewSuffix('txt');

        // The options we are accepting for deleting
        $options = new Zend_Console_Getopt(
            array(
                'email|e=s' => $this->tr->_('EMAIL_USERNAME'),
            )
        );

        try {
            $options->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            $this->view->message = $e->getUsageMessage();
            return;
        }
        if ($options->email == '') {
            echo $options->getUsageMessage();
            exit();
        }

        $partner_email = strtolower($options->email);

        $model        = new Default_Model_Partner();
        $tempPartners = $model->getAll();
        $partner_id   = null;
        foreach ($tempPartners as $key => $value) {
            if ($partner_email == $value['email']) {
                $partner_id = $value['hash'];
                break;
            }
        }

        if (!$partner_id) {
            $this->view->message = $this->tr->_('COULD_NOT_DELETE_PARTNER') . ': ' . $partner_email . '. ' . $this->tr->_('COULD_NOT_FIND_MATCH') . PHP_EOL;
            return;
        }

        try {
            $model->delete($partner_id);
            $this->view->message = $this->tr->_('SUCCESS_DELETE_PARTNER') . ': ' . $partner_email . PHP_EOL;
        } catch (RuntimeException $e) {
            $this->view->message = $this->tr->_('ERROR_DELETING_PARTNER') . ': ' . $partner_email . '. ' . $e->getMessage() . PHP_EOL;
        }

    }
}
