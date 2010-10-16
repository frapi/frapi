<?php

class Lupin_Model_API extends Lupin_Model
{
    protected $hostname = '';
    private $responseCode = 200;

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
    }

    protected function doGet($action, array $query)
    {
        $module = empty($action) ? substr($this->module, 0, -1) : $this->module;
        if (strrpos($action, '.json') === false) {
            $action .= '.json';
        }

        array_walk_recursive($query, 'Lupin_Model_API::encode');
        $url = $this->hostname . $module . $action;

        $request = new HttpRequest($url, HTTP_METH_GET);
        $request->setQueryData($query);

        try {
            $request->send();
        } catch (Exception $e) {
            return false;
        }

        $this->responseCode = $request->getResponseCode();
        if ($request->getResponseCode() !== 200) {
            return false;
        }

        $json = json_decode($request->getResponseBody());
        if (!is_object($json) && !is_array($json)) {
            return false;
        }

        return $json;
    }

    protected function doPost($action, array $data)
    {
        $module = empty($action) ? substr($this->module, 0, -1) : $this->module;
        if (strrpos($action, '.json') === false) {
            $action .= '.json';
        }

        array_walk_recursive($data, 'Lupin_Model_API::encode');
        $url = $this->hostname. $module . $action;
        $request = new HttpRequest($url, HTTP_METH_POST);
        $request->setPostFields($data);

        try {
            $request->send();
        } catch (Exception $e) {
            return false;
        }

        $this->responseCode = $request->getResponseCode();
        if ($request->getResponseCode() !== 200) {
            return false;
        }

        $json = json_decode($request->getResponseBody());
        if (!is_object($json) && !is_array($json)) {
            return false;
        }

        return $json;
    }

    protected function doHead($action, array $data)
    {
        $module = empty($action) ? substr($this->module, 0, -1) : $this->module;
        if (strrpos($action, '.json') === false) {
            $action .= '.json';
        }

        array_walk_recursive($data, 'Lupin_Model_API::encode');
        $url = $this->hostname. $module . $action;
        $request = new HttpRequest($url, HTTP_METH_HEAD);
        $request->setQueryData($data);

        try {
            $request->send();
        } catch (Exception $e) {
            return false;
        }

        $this->responseCode = $request->getResponseCode();
        if ($request->getResponseCode() !== 200) {
            return false;
        }

        return $request->getResponseHeader();
    }

    protected function doDelete($action, array $data)
    {
        $module = empty($action) ? substr($this->module, 0, -1) : $this->module;
        if (strrpos($action, '.json') === false) {
            $action .= '.json';
        }

        array_walk_recursive($data, 'Lupin_Model_API::encode');
        $url = $this->hostname. $module . $action;
        $request = new HttpRequest($url, HTTP_METH_DELETE);
        $request->setQueryData($data);

        try {
            $request->send();
        } catch (Exception $e) {
            return false;
        }

        $this->responseCode = $request->getResponseCode();
        if ($request->getResponseCode() !== 200) {
            return false;
        }

        return $request->getResponseHeader();
    }

    public static function encode(&$value, $foo)
    {
        if (is_string($value)) {
            return rawurlencode($value);
        }

        return $value;
    }

    /**
     * Encapsulates the insertions and updates within a DB transation
     *
     * @param array $data    The data being inserted in the database
     * @param mixed $primary The primary key value if updating
     *
     * @return bool
     */
    public function save(array $data, $id = null)
    {
        $res = false;
        try {
            $res = $this->_save($data, $id);
        } catch(Exception $e) {
            if (APPLICATION_ENV === 'development') {
                echo '<pre>'; print_r($e);exit;
            }
        }

        return $res;
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }
}
