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
class ActionController extends Zend_Controller_Action
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
        $this->_helper->viewRenderer->setViewSuffix('txt');

        $model               = new Default_Model_Action();
        $this->view->actions = $model->getAll();
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
        $this->_helper->viewRenderer->setViewSuffix('txt');

        // The options we are accepting for adding
        $options = new Zend_Console_Getopt(
            array(
                'name|n=s'                 => 'Name of the action.',
                'enabled|e'                => 'Is the action enabled?',
                'public|p'                 => 'Is the action public?',
                'route|r=s'                => 'Custom route of the action.',
                'description|d=s'          => 'Description of the action.',
                'parameters|pa=s'          => 'List of comma-seperated optional parameters.',
                'required-parameters|rp=s' => 'List of comma-seperated required parameters.'
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
        } else if ($options->route == '') {
            $this->view->message = $options->getUsageMessage();
            return;
        }

        $action_name               = $options->name;
        $action_enabled            = $options->enabled === true ? '1' : '0';
        $action_public             = $options->public === true ? '1' : '0';
        $action_route              = $options->route;
        $action_description        = $options->description;

        $submit_data = array (
            'name'              => $action_name,
            'enabled'           => $action_enabled,
            'public'            => $action_public,
            'route'             => $action_route,
            'description'       => $action_description
        );

        // Handle parameters passed
        $action_optional_parameters = explode(',', $options->parameters);
        $i = 0;
        foreach ($action_optional_parameters as $parameter) {
            if ($parameter != '') {
                $submit_data['param'][$i] = $parameter;
                $i++;
            }
        }

        $action_required_parameters = explode(',', $options->getOption('required-parameters'));
        foreach ($action_required_parameters as $parameter) {
            if ($parameter != '') {
                $submit_data['param'][$i]    = $parameter;
                $submit_data['required'][$i] = '1';
                $i++;
            }
        }

        $model = new Default_Model_Action();
        try {
            $model->add($submit_data);
            $this->view->message = 'Successfully added action: ' . $action_name . PHP_EOL;
        } catch (RuntimeException $e) {
            $this->view->message = 'Error adding action: ' . $action_name . '. ' . $e->getMessage() . PHP_EOL;
        }
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
        $this->_helper->viewRenderer->setViewSuffix('txt');

        // The options we are accepting for deleting
        $options = new Zend_Console_Getopt(
            array(
                'name|n=s' => 'Name of the action.',
            )
        );

        try {
            $options->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            $this->view->message = $e->getUsageMessage();
            return;
        }
        if ($options->name == '') {
            echo $options->getUsageMessage();
            exit();
        }

        $action_name = ucfirst(strtolower($options->name));

        $model       = new Default_Model_Action();
        $tempActions = $model->getList();
        $action_id   = null;
        foreach ($tempActions as $hash => $tempName) {
            if ($action_name == $tempName) {
                $action_id = $hash;
                break;
            }
        }

        if (!$action_id) {
            $this->view->message = 'Could not delete action: ' . $action_name . '. Could not find match.' . PHP_EOL;
            return;
        }

        try {
            $model->delete($action_id);
            $this->view->message = 'Successfully deleted action: ' . $action_name . PHP_EOL;
        } catch (RuntimeException $e) {
            $this->view->message = 'Error deleting action: ' . $action_name . '. ' . $e->getMessage() . PHP_EOL;
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
        $this->_helper->viewRenderer->setViewSuffix('txt');

        $dir  = ROOT_PATH . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'Action';

        if (!is_writable($dir)) {
            $this->view->message = 'The path : "' . $dir
                . '" is not currently writeable by this user, '
                . 'therefore we cannot synchronize the codebase' . PHP_EOL;
           return;
        }

        $model = new Default_Model_Action();

        try {
            $model->sync();
            $this->view->message = 'All actions have been synced successfully.' . PHP_EOL;
        } catch (RuntimeException $e) {
            $this->view->message = 'Error synchronizing actions. ' . $e->getMessage();
        }
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
        $this->_helper->viewRenderer->setViewSuffix('txt');
        // The options we are accepting for adding
        $options = new Zend_Console_Getopt(
            array(
                'name|n=s'       => 'Name of the action to call.',
                'parameters|p=s' => 'Paramters to use. For example var1=val1&var2=val2',
                'format|f=s'     => 'Format to return. Defaults to XML.',
                'method|m=s'     => 'Method to use. Defaults to GET.',
                'email|e=s'      => 'Email or username to use.',
                'secretkey|sk=s' => 'Secret key associated with email passed.',
                'domain|d=s'     => 'Domain to use, if not included will use default',
                'query-uri|u=s'  => 'Query uri to use. For example /testing/1',
                'https|h'        => 'Use https.',
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

        $confModel   = new Default_Model_Configuration();
        if (!$confModel->getKey('api_url')) {
            $this->view->message = 'Remember you can set the default API domain name in your admin configuration.' . PHP_EOL;
        }

        if (!class_exists('HttpRequest')) {
            $this->view->message = 'HttpRequest class was not found the pecl_http (http://pecl.php.net/package/pecl_http) package is required to use the tester.' . PHP_EOL;
            return;
        }

        $action_name   = $options->name;
        $params        = $options->parameters;
        $format        = $options->format;
        $method        = $options->method;
        $email         = $options->email;
        $password      = $options->secretkey;
        $url           = $options->domain;
        $ssl           = $options->https;
        $query_uri     = $options->getOption('query-uri');

        if ($url == '') {
            $url = $confModel->getKey('api_url');
        }

        if ($query_uri == '') {
            $actionModel = new Default_Model_Action();
            $actions     = $actionModel->getAll();

            foreach( $actions as $action_details) {
                if ($action_details['name'] == $action_name) {
                    $query_uri = $action_details['route'];
                }
            }
        }

        $newMethod = HTTP_METH_GET;

        switch (strtolower($method)) {
            case 'get':
                $newMethod = HTTP_METH_GET;
                break;

            case 'post':
                $newMethod = HTTP_METH_POST;
                break;

            case 'put':
                $newMethod = HTTP_METH_PUT;
                break;

            case 'delete':
                $newMethod = HTTP_METH_DELETE;
                break;

            case 'head':
                $newMethod = HTTP_METH_HEAD;
                break;
        }

        $request_url = 'http' . ($ssl !== null ? 's' : '') . '://' . $url . '/' . $query_uri . '.' . strtolower($format);

        $httpOptions = array();

        if ($email && $password) {
            $httpOptions = array(
                'headers'      => array('Accept' => '*/*'),
                'httpauth'     => $email . ':' . $password,
                'httpauthtype' => HTTP_AUTH_DIGEST,
            );
        }

        $request = new HttpRequest($request_url, $newMethod, $httpOptions);

        if ("POST" == strtoupper($method)) {
            $request->setBody($params);
        } else {
            $request->setQueryData($params);
        }

        $res = $request->send();

        $responseInfo                    = $request->getResponseInfo();
        $this->view->request_url         = $responseInfo['effective_url'];
        $this->view->response_header     = $this->collapseHeaders($res->getHeaders());
        $this->view->content             = $res->getBody();
        $this->view->status              = $res->getResponseCode();
        $this->view->method              = isset($method) ? strtoupper($method) : 'GET';
        $this->view->request_post_fields = ($newMethod == HTTP_METH_POST) ? $params : '';

    }

    protected function collapseHeaders($headers)
    {
        $header_string = "";
        foreach ($headers as $name => $value) {
            if (is_array($value)) {
                $value = implode("\n\t", $value);
            }

            $header_string .= $name . ": " . wordwrap($value, 45, "\n\t") . "\n";
        }
        return $header_string;
    }
}
