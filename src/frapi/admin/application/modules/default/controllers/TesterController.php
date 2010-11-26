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
class TesterController extends Lupin_Controller_Base
{
    private $tr;

    public function init($styles = array())
    {
        $this->tr = Zend_Registry::get('tr');
        $actions = array('index', 'ajax');
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

        if (!class_exists("HttpRequest")) {
            $this->addErrorMessage($this->tr->_('TESTER_HTTP_REQUEST_MISSING'));
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

        if (!in_array($session_query_uri, $history)) {
            $history[] = $session_query_uri;
        }

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

        $newMethod = HTTP_METH_GET;

        switch ($method) {
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

        $email = $this->_request->getParam('email');
        $pass = $this->_request->getParam('secretKey');

        $request_url = 'http' . ($ssl !== null ? 's' : '') . '://' . $url . '/' . $query_uri;

        $httpOptions = array();

        if ($email && $pass) {
            $httpOptions = array(
                'headers'      => array('Accept' => '*/*'),
                'httpauth'     => $email . ':' . $pass,
                'httpauthtype' => HTTP_AUTH_DIGEST,
            );
        }

        $request = new HttpRequest($request_url, $newMethod, $httpOptions);

        if ("post" == $method) {
            $request->addPostFields($params);
        } else {
            $request->addQueryData($params);
        }

        $res = $request->send();

        $responseInfo = $request->getResponseInfo();
        $response = array(
            'request_url'         => $responseInfo['effective_url'],
            'response_headers'    => $this->collapseHeaders($res->getHeaders()),
            'content'             => $res->getBody(),
            'status'              => $res->getResponseCode(),
            'method'              => strtoupper($method),
            'request_post_fields' => http_build_query(
                !is_null($postFields = $request->getPostFields()) ? $postFields : array()
            )
        );

        $this->view->renderJson($response);
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
