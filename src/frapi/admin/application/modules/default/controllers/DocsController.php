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
 * @package   frapi-admin
 */
class DocsController extends Lupin_Controller_Base
{
    public function init($styles = array())
    {
        $actions = array('index', 'generate');
        $this->_helper->_acl->allow('admin', $actions);

        $contextSwitch = $this->_helper->getHelper('contextSwitch');

        if (!$contextSwitch->hasContext('text')) {
            $contextSwitch->addContext(
                'text', array(
                    'suffix'  => 'txt',
                    'headers' => array('Content-Type'=>'text/plain')
                )
            );
            $contextSwitch->addContext(
                'mdown', array(
                    'suffix'  => 'mdown',
                    'headers' => array('Content-Type'=>'text/plain')
                )
            );
            $contextSwitch->addContext(
                'html', array(
                    'suffix'  => 'html',
                    'headers' => array('Content-Type'=>'text/html')
                )
            );

            $contextSwitch->addContext(
                'pdf', array(
                    'suffix'  => 'pdf',
                    'headers' => array(
                        'Content-Type'=>'application/pdf',
                        'Content-Disposition' => 'Attachment; filename="api_docs-'.@date('Y-m-d').'.pdf"',
                    )
                )
            );
        }

        $contextSwitch->addActionContext('generate', 'text')->initContext();
        $contextSwitch->addActionContext('generate', 'html')->initContext();
        $contextSwitch->addActionContext('generate', 'pdf')->initContext();
        $contextSwitch->addActionContext('generate', 'mdown')->initContext();

        parent::init($styles);
    }

    /**
     * Index, used to display format options and
     * basic outline.
     *
     * @return void
     **/
    public function indexAction()
    {
        $this->_forward('generate');
    }

    /**
     * Generate documentation, in available formats.
     *
     * @return void
     **/
    public function generateAction()
    {
        $doc_data = array();
        $emod  = new Default_Model_Error;
        $amod  = new Default_Model_Action;
        $omod  = new Default_Model_Output;
        $cmod  = new Default_Model_Configuration();

        $doc_data['actions']      = $amod->getAll();
        $doc_data['output-types'] = $omod->getAll();
        $doc_data['errors']       = $emod->getAll();
        $doc_data['base_url']     = $cmod->getKey('api_url');

        $this->view->doc_data     = $doc_data;
    }
}
