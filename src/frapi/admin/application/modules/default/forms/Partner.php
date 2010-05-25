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
class Default_Form_Partner extends Lupin_Form
{
    public function init()
    {
        $name = new Zend_Form_Element_Text('firstname');
        $name->setLabel('First Name');
        $name->setRequired(true);
        $this->addElement($name);

        $lastname = new Zend_Form_Element_Text('lastname');
        $lastname->setLabel('Last Name');
        $lastname->setRequired(true);
        $this->addElement($lastname);

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email/Username');
        $email->setRequired(true);
        $this->addElement($email);

        $company = new Zend_Form_Element_Text('company');
        $company->setLabel('Company');
        $company->setRequired(true);
        $this->addElement($company);
    }
}
