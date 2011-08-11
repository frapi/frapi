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
class TesterController extends Lupin_Controller_Base
{
    private $tr;

    public function init($styles = array())
    {
        $this->tr = Zend_Registry::get('tr');
        $actions = array('index', 'ajax', 'history');
        $this->_helper->_acl->allow('admin', $actions);
        parent::init($styles);
    }

    public function indexAction()
    {
        $request     = $this->getRequest();
        $form        = new Default_Form_Tester;

        $confModel   = new Default_Model_Configuration();
        if (!$confModel->getKey("api_url")) {
            $this->addInfoMessage($this->tr->_('TESTER_API_INFO_MESSAGE'));
        }

        $this->view->form = $form;
    }

    public function ajaxAction()
    {
        $this->view  = new Lupin_View();

        $method      = strtolower($this->_request->getParam('method'));
        $query_uri   = trim($this->_request->getParam('query_uri'), '/ ');
        $url         = $this->_request->getParam('url');
        $ssl         = $this->_request->getParam('ssl');
        $extraParams = $this->_request->getParam('param');

        $params      = array();

        $session_query_uri = '/' . substr($query_uri, 0, strrpos($query_uri, '.'));
        $test_history      = new Zend_Session_Namespace('test_history');
        $history           = $test_history->value;

        $history[$session_query_uri] = $this->getRequest()->getParams();

        $test_history->value = $history;

        if (!empty($extraParams)) {
            foreach ($extraParams as $newParam) {
                $parms                 = explode('=', $newParam, 2);
                if (count($parms) > 1) {
                    list($key, $value) = $parms;
                    $params[$key]      = $value;
                }
            }
        }

        require_once 'HTTP/Request2.php';

        $newMethod = HTTP_Request2::METHOD_GET;

        switch ($method) {
            case 'get':
                $newMethod = HTTP_Request2::METHOD_GET;
                break;
            case 'post':
                $newMethod = HTTP_Request2::METHOD_POST;
                break;
            case 'put':
                $newMethod = HTTP_Request2::METHOD_PUT;
                break;
            case 'delete':
                $newMethod = HTTP_Request2::METHOD_DELETE;
                break;
            case 'head':
                $newMethod = HTTP_Request2::METHOD_HEAD;

                break;
        }

        $email = $this->_request->getParam('email');
        $pass = $this->_request->getParam('secretKey');

        $request_url = 'http' . ($ssl == true ? 's' : '') . '://' . $url . '/' . $query_uri;

        $request = new HTTP_Request2($request_url, $newMethod);
        $request->setConfig(array(
            'ssl_verify_peer' => false,
            'ssl_verify_host' => false,
        ));

        if ($email && $pass) {
            $request->setAuth($email, $pass, HTTP_Request2::AUTH_DIGEST);
            $request->setHeader(array(
                'Accept' => '*/*'
            ));
        }
        if ($method == 'post') {
            $request->addPostParameter($params);
        } else {
            $url = $request->getUrl();
            $url->setQueryVariables(array() + $params);
        }

        try {
            $res = $request->send();
        } catch (Exception $e) {
            return $this->view->renderJson(array(
                'request_url' => $request_url,
                'response_headers' => $this->collapseHeaders(array(
                    'error' => $e->getMessage())
                ),

                'content' => $e->getMessage(),
                'status' => 'Could not connect',
                'method' => strtoupper($method)
            ));
        }

        $response = array(
            'request_url'         => $request_url,
            'response_headers'    => $this->collapseHeaders($res->getHeader()),
            'content'             => $res->getBody(),
            'status'              => $res->getStatus(),
            'method'              => strtoupper($method),
        );

        $this->view->renderJson($response);
    }

    public function historyAction()
    {
        $this->view  = new Lupin_View();

        $url = strtolower($this->_request->getParam('url'));

        $test_history             = new Zend_Session_Namespace('test_history');
        $history                  = $test_history->value;
        $return_data              = $history[$url];
        $return_data['format']    = substr($return_data['query_uri'], strrpos($return_data['query_uri'], '.') +1);
        $return_data['query_uri'] = substr($return_data['query_uri'], 0, strrpos($return_data['query_uri'], '.'));

        $this->view->renderJson($return_data);
    }

    protected function collapseHeaders($headers)
    {
        $header_string = "";

        foreach ($headers as $name => $value) {
            if (is_array($value)) {
                $value = implode("\n\t", $value);
            }

            $header_string .= ucfirst($name) . ": " . wordwrap($value, 45, "\n\t") . "\n";
        }
        return $header_string;
    }
}
