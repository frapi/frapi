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
class PartnerController
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
        $model      = new Default_Model_Partner();
        $partners = $model->getAll();

        // Determine our max field widths so we can pad things out appropriately
        $partner_first_name_max_length = strlen('First Name ');
        $partner_last_name_max_length  = strlen('Last Name ');
        $partner_email_max_length      = strlen('Email ');
        $partner_company_max_length    = strlen('Company ');

        if ($partners) {
            foreach ($partners as $key => $partner) {

                if (strlen($partner['firstname']) > $partner_first_name_max_length) {
                    $partner_first_name_max_length = strlen($partner['firstname']) + 1;
                }

                if (strlen($partner['lastname']) > $partner_last_name_max_length) {
                    $partner_last_name_max_length = strlen($partner['lastname']) + 1;
                }

                if (strlen($partner['email']) > $partner_email_max_length) {
                    $partner_email_max_length = strlen($partner['email']) + 1;
                }

                if (strlen($partner['company']) > $partner_company_max_length) {
                    $partner_company_max_length = strlen($partner['company']) + 1;
                }
            }
        }
        echo  str_pad('First Name', $partner_first_name_max_length) .
            str_pad('Last Name', $partner_last_name_max_length) .
            str_pad('Email', $partner_email_max_length) .
            str_pad('Company', $partner_company_max_length) . PHP_EOL;

        if ($partners) {
            foreach ($partners as $key => $partner) {
                echo str_pad($partner['firstname'], $partner_first_name_max_length) .
                    str_pad($partner['lastname'], $partner_last_name_max_length) .
                    str_pad($partner['email'], $partner_email_max_length) .
                    str_pad($partner['company'], $partner_company_max_length) . PHP_EOL;
            }
        }
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
            echo $e->getUsageMessage();
            exit();
        }

        if ($options->getOption('first-name') == ''
            || $options->getOption('last-name') == ''
            || $options->email == ''
            || $options->company == '') {
            echo $options->getUsageMessage();
            exit();
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
            echo 'Successfully added partner: ' . $partner_email . PHP_EOL;
        } catch (RuntimeException $e) {
            echo 'Error adding partner: ' . $partner_email . '. ' . $e->getMessage() . PHP_EOL;
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
        // The options we are accepting for deleting
        $options = new Zend_Console_Getopt(
            array(
                'email|e=s' => 'Username/Email of the partner.',
            )
        );

        try {
            $options->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            echo $e->getUsageMessage();
            exit();
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
            echo 'Could not delete partner: ' . $partner_email . '. Could not find match.' . PHP_EOL;
            exit();
        }

        try {
            $model->delete($partner_id);
            echo 'Successfully deleted partner: ' . $partner_email . PHP_EOL;
        } catch (RuntimeException $e) {
            echo 'Error deleting partner: ' . $partner_email . '. ' . $e->getMessage() . PHP_EOL;
        }

    }
}
