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
class Default_Form_Configuration extends Lupin_Form
{
    public function init()
    {
        $api_url = new Zend_Form_Element_Text('api_url');
        $api_url->setLabel('API Domain (for Tester)');
        $api_url->setRequired(true);
        $this->addElement($api_url);
        
        $this->addElement(new Zend_Form_Element_Submit('Update Configuration'));
        
        parent::init();
    }
}