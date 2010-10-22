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
class PartnerController extends Zend_Controller_Action
{

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
                'first-name|fn=s' => 'First Name of the partner.',
                'last-name|ln=s'  => 'Last Name of the partner.',
                'email|e=s'    => 'Username/Email of the partner.',
                'company|c=s'     => 'Company of the partner.',
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
                'email|e=s' => 'Username/Email of the partner.',
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
            $this->view->message = 'Could not delete partner: ' . $partner_email . '. Could not find match.' . PHP_EOL;
            return;
        }

        try {
            $model->delete($partner_id);
            $this->view->message = 'Successfully deleted partner: ' . $partner_email . PHP_EOL;
        } catch (RuntimeException $e) {
            $this->view->message = 'Error deleting partner: ' . $partner_email . '. ' . $e->getMessage() . PHP_EOL;
        }

    }
}
