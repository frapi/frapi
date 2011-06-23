<?php
/**
 * Adapter for HTTP_Request2 wrapping around cURL extension
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright (c) 2008-2011, Alexey Borzov <avb@php.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The names of the authors may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   HTTP
 * @package    HTTP_Request2
 * @author     Alexey Borzov <avb@php.net>
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id: Curl.php 310800 2011-05-06 07:29:56Z avb $
 * @link       http://pear.php.net/package/HTTP_Request2
 */

/**
 * Base class for HTTP_Request2 adapters
 */
require_once 'HTTP/Request2/Adapter.php';

/**
 * Adapter for HTTP_Request2 wrapping around cURL extension
 *
 * @category    HTTP
 * @package     HTTP_Request2
 * @author      Alexey Borzov <avb@php.net>
 * @version     Release: 2.0.0RC1
 */
class HTTP_Request2_Adapter_Curl extends HTTP_Request2_Adapter
{
   /**
    * Mapping of header names to cURL options
    * @var  array
    */
    protected static $headerMap = array(
        'accept-encoding' => CURLOPT_ENCODING,
        'cookie'          => CURLOPT_COOKIE,
        'referer'         => CURLOPT_REFERER,
        'user-agent'      => CURLOPT_USERAGENT
    );

   /**
    * Mapping of SSL context options to cURL options
    * @var  array
    */
    protected static $sslContextMap = array(
        'ssl_verify_peer' => CURLOPT_SSL_VERIFYPEER,
        'ssl_cafile'      => CURLOPT_CAINFO,
        'ssl_capath'      => CURLOPT_CAPATH,
        'ssl_local_cert'  => CURLOPT_SSLCERT,
        'ssl_passphrase'  => CURLOPT_SSLCERTPASSWD
   );

   /**
    * Mapping of CURLE_* constants to Exception subclasses and error codes
    * @var  array
    */
    protected static $errorMap = array(
        CURLE_UNSUPPORTED_PROTOCOL  => array('HTTP_Request2_MessageException',
                                             HTTP_Request2_Exception::NON_HTTP_REDIRECT),
        CURLE_COULDNT_RESOLVE_PROXY => array('HTTP_Request2_ConnectionException'),
        CURLE_COULDNT_RESOLVE_HOST  => array('HTTP_Request2_ConnectionException'),
        CURLE_COULDNT_CONNECT       => array('HTTP_Request2_ConnectionException'),
        // error returned from write callback
        CURLE_WRITE_ERROR           => array('HTTP_Request2_MessageException',
                                             HTTP_Request2_Exception::NON_HTTP_REDIRECT),
        CURLE_OPERATION_TIMEOUTED   => array('HTTP_Request2_MessageException',
                                             HTTP_Request2_Exception::TIMEOUT),
        CURLE_HTTP_RANGE_ERROR      => array('HTTP_Request2_MessageException'),
        CURLE_SSL_CONNECT_ERROR     => array('HTTP_Request2_ConnectionException'),
        CURLE_LIBRARY_NOT_FOUND     => array('HTTP_Request2_LogicException',
                                             HTTP_Request2_Exception::MISCONFIGURATION),
        CURLE_FUNCTION_NOT_FOUND    => array('HTTP_Request2_LogicException',
                                             HTTP_Request2_Exception::MISCONFIGURATION),
        CURLE_ABORTED_BY_CALLBACK   => array('HTTP_Request2_MessageException',
                                             HTTP_Request2_Exception::NON_HTTP_REDIRECT),
        CURLE_TOO_MANY_REDIRECTS    => array('HTTP_Request2_MessageException',
                                             HTTP_Request2_Exception::TOO_MANY_REDIRECTS),
        CURLE_SSL_PEER_CERTIFICATE  => array('HTTP_Request2_ConnectionException'),
        CURLE_GOT_NOTHING           => array('HTTP_Request2_MessageException'),
        CURLE_SSL_ENGINE_NOTFOUND   => array('HTTP_Request2_LogicException',
                                             HTTP_Request2_Exception::MISCONFIGURATION),
        CURLE_SSL_ENGINE_SETFAILED  => array('HTTP_Request2_LogicException',
                                             HTTP_Request2_Exception::MISCONFIGURATION),
        CURLE_SEND_ERROR            => array('HTTP_Request2_MessageException'),
        CURLE_RECV_ERROR            => array('HTTP_Request2_MessageException'),
        CURLE_SSL_CERTPROBLEM       => array('HTTP_Request2_LogicException',
                                             HTTP_Request2_Exception::INVALID_ARGUMENT),
        CURLE_SSL_CIPHER            => array('HTTP_Request2_ConnectionException'),
        CURLE_SSL_CACERT            => array('HTTP_Request2_ConnectionException'),
        CURLE_BAD_CONTENT_ENCODING  => array('HTTP_Request2_MessageException'),
    );

   /**
    * Response being received
    * @var  HTTP_Request2_Response
    */
    protected $response;

   /**
    * Whether 'sentHeaders' event was sent to observers
    * @var  boolean
    */
    protected $eventSentHeaders = false;

   /**
    * Whether 'receivedHeaders' event was sent to observers
    * @var boolean
    */
    protected $eventReceivedHeaders = false;

   /**
    * Position within request body
    * @var  integer
    * @see  callbackReadBody()
    */
    protected $position = 0;

   /**
    * Information about last transfer, as returned by curl_getinfo()
    * @var  array
    */
    protected $lastInfo;

   /**
    * Creates a subclass of HTTP_Request2_Exception from curl error data
    *
    * @param resource curl handle
    * @return HTTP_Request2_Exception
    */
    protected static function wrapCurlError($ch)
    {
        $nativeCode = curl_errno($ch);
        $message    = 'Curl error: ' . curl_error($ch);
        if (!isset(self::$errorMap[$nativeCode])) {
            return new HTTP_Request2_Exception($message, 0, $nativeCode);
        } else {
            $class = self::$errorMap[$nativeCode][0];
            $code  = empty(self::$errorMap[$nativeCode][1])
                     ? 0 : self::$errorMap[$nativeCode][1];
            return new $class($message, $code, $nativeCode);
        }
    }

   /**
    * Sends request to the remote server and returns its response
    *
    * @param    HTTP_Request2
    * @return   HTTP_Request2_Response
    * @throws   HTTP_Request2_Exception
    */
    public function sendRequest(HTTP_Request2 $request)
    {
        if (!extension_loaded('curl')) {
            throw new HTTP_Request2_LogicException(
                'cURL extension not available', HTTP_Request2_Exception::MISCONFIGURATION
            );
        }

        $this->request              = $request;
        $this->response             = null;
        $this->position             = 0;
        $this->eventSentHeaders     = false;
        $this->eventReceivedHeaders = false;

        try {
            if (false === curl_exec($ch = $this->createCurlHandle())) {
                $e = self::wrapCurlError($ch);
            }
        } catch (Exception $e) {
        }
        if (isset($ch)) {
            $this->lastInfo = curl_getinfo($ch);
            curl_close($ch);
        }

        $response = $this->response;
        unset($this->request, $this->requestBody, $this->response);

        if (!empty($e)) {
            throw $e;
        }

        if ($jar = $request->getCookieJar()) {
            $jar->addCookiesFromResponse($response, $request->getUrl());
        }

        if (0 < $this->lastInfo['size_download']) {
            $request->setLastEvent('receivedBody', $response);
        }
        return $response;
    }

   /**
    * Returns information about last transfer
    *
    * @return   array   associative array as returned by curl_getinfo()
    */
    public function getInfo()
    {
        return $this->lastInfo;
    }

   /**
    * Creates a new cURL handle and populates it with data from the request
    *
    * @return   resource    a cURL handle, as created by curl_init()
    * @throws   HTTP_Request2_LogicException
    */
    protected function createCurlHandle()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            // setup write callbacks
            CURLOPT_HEADERFUNCTION => array($this, 'callbackWriteHeader'),
            CURLOPT_WRITEFUNCTION  => array($this, 'callbackWriteBody'),
            // buffer size
            CURLOPT_BUFFERSIZE     => $this->request->getConfig('buffer_size'),
            // connection timeout
            CURLOPT_CONNECTTIMEOUT => $this->request->getConfig('connect_timeout'),
            // save full outgoing headers, in case someone is interested
            CURLINFO_HEADER_OUT    => true,
            // request url
            CURLOPT_URL            => $this->request->getUrl()->getUrl()
        ));

        // set up redirects
        if (!$this->request->getConfig('follow_redirects')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        } else {
            if (!@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true)) {
                throw new HTTP_Request2_LogicException(
                    'Redirect support in curl is unavailable due to open_basedir or safe_mode setting',
                    HTTP_Request2_Exception::MISCONFIGURATION
                );
            }
            curl_setopt($ch, CURLOPT_MAXREDIRS, $this->request->getConfig('max_redirects'));
            // limit redirects to http(s), works in 5.2.10+
            if (defined('CURLOPT_REDIR_PROTOCOLS')) {
                curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            }
            // works in 5.3.2+, http://bugs.php.net/bug.php?id=49571
            if ($this->request->getConfig('strict_redirects') && defined('CURLOPT_POSTREDIR')) {
                curl_setopt($ch, CURLOPT_POSTREDIR, 3);
            }
        }

        // request timeout
        if ($timeout = $this->request->getConfig('timeout')) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        }

        // set HTTP version
        switch ($this->request->getConfig('protocol_version')) {
            case '1.0':
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
                break;
            case '1.1':
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        }

        // set request method
        switch ($this->request->getMethod()) {
            case HTTP_Request2::METHOD_GET:
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case HTTP_Request2::METHOD_POST:
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case HTTP_Request2::METHOD_HEAD:
                curl_setopt($ch, CURLOPT_NOBODY, true);
                break;
            case HTTP_Request2::METHOD_PUT:
                curl_setopt($ch, CURLOPT_UPLOAD, true);
                break;
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->request->getMethod());
        }

        // set proxy, if needed
        if ($host = $this->request->getConfig('proxy_host')) {
            if (!($port = $this->request->getConfig('proxy_port'))) {
                throw new HTTP_Request2_LogicException(
                    'Proxy port not provided', HTTP_Request2_Exception::MISSING_VALUE
                );
            }
            curl_setopt($ch, CURLOPT_PROXY, $host . ':' . $port);
            if ($user = $this->request->getConfig('proxy_user')) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user . ':' .
                            $this->request->getConfig('proxy_password'));
                switch ($this->request->getConfig('proxy_auth_scheme')) {
                    case HTTP_Request2::AUTH_BASIC:
                        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
                        break;
                    case HTTP_Request2::AUTH_DIGEST:
                        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_DIGEST);
                }
            }
        }

        // set authentication data
        if ($auth = $this->request->getAuth()) {
            curl_setopt($ch, CURLOPT_USERPWD, $auth['user'] . ':' . $auth['password']);
            switch ($auth['scheme']) {
                case HTTP_Request2::AUTH_BASIC:
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    break;
                case HTTP_Request2::AUTH_DIGEST:
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            }
        }

        // set SSL options
        foreach ($this->request->getConfig() as $name => $value) {
            if ('ssl_verify_host' == $name && null !== $value) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $value? 2: 0);
            } elseif (isset(self::$sslContextMap[$name]) && null !== $value) {
                curl_setopt($ch, self::$sslContextMap[$name], $value);
            }
        }

        $headers = $this->request->getHeaders();
        // make cURL automagically send proper header
        if (!isset($headers['accept-encoding'])) {
            $headers['accept-encoding'] = '';
        }

        if (($jar = $this->request->getCookieJar())
            && ($cookies = $jar->getMatching($this->request->getUrl(), true))
        ) {
            $headers['cookie'] = (empty($headers['cookie'])? '': $headers['cookie'] . '; ') . $cookies;
        }

        // set headers having special cURL keys
        foreach (self::$headerMap as $name => $option) {
            if (isset($headers[$name])) {
                curl_setopt($ch, $option, $headers[$name]);
                unset($headers[$name]);
            }
        }

        $this->calculateRequestLength($headers);
        if (isset($headers['content-length'])) {
            $this->workaroundPhpBug47204($ch, $headers);
        }

        // set headers not having special keys
        $headersFmt = array();
        foreach ($headers as $name => $value) {
            $canonicalName = implode('-', array_map('ucfirst', explode('-', $name)));
            $headersFmt[]  = $canonicalName . ': ' . $value;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headersFmt);

        return $ch;
    }

   /**
    * Workaround for PHP bug #47204 that prevents rewinding request body
    *
    * The workaround consists of reading the entire request body into memory
    * and setting it as CURLOPT_POSTFIELDS, so it isn't recommended for large
    * file uploads, use Socket adapter instead.
    *
    * @param    resource    cURL handle
    * @param    array       Request headers
    */
    protected function workaroundPhpBug47204($ch, &$headers)
    {
        // no redirects, no digest auth -> probably no rewind needed
        if (!$this->request->getConfig('follow_redirects')
            && (!($auth = $this->request->getAuth())
                || HTTP_Request2::AUTH_DIGEST != $auth['scheme'])
        ) {
            curl_setopt($ch, CURLOPT_READFUNCTION, array($this, 'callbackReadBody'));

        // rewind may be needed, read the whole body into memory
        } else {
            if ($this->requestBody instanceof HTTP_Request2_MultipartBody) {
                $this->requestBody = $this->requestBody->__toString();

            } elseif (is_resource($this->requestBody)) {
                $fp = $this->requestBody;
                $this->requestBody = '';
                while (!feof($fp)) {
                    $this->requestBody .= fread($fp, 16384);
                }
            }
            // curl hangs up if content-length is present
            unset($headers['content-length']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
        }
    }

   /**
    * Callback function called by cURL for reading the request body
    *
    * @param    resource    cURL handle
    * @param    resource    file descriptor (not used)
    * @param    integer     maximum length of data to return
    * @return   string      part of the request body, up to $length bytes
    */
    protected function callbackReadBody($ch, $fd, $length)
    {
        if (!$this->eventSentHeaders) {
            $this->request->setLastEvent(
                'sentHeaders', curl_getinfo($ch, CURLINFO_HEADER_OUT)
            );
            $this->eventSentHeaders = true;
        }
        if (in_array($this->request->getMethod(), self::$bodyDisallowed) ||
            0 == $this->contentLength || $this->position >= $this->contentLength
        ) {
            return '';
        }
        if (is_string($this->requestBody)) {
            $string = substr($this->requestBody, $this->position, $length);
        } elseif (is_resource($this->requestBody)) {
            $string = fread($this->requestBody, $length);
        } else {
            $string = $this->requestBody->read($length);
        }
        $this->request->setLastEvent('sentBodyPart', strlen($string));
        $this->position += strlen($string);
        return $string;
    }

   /**
    * Callback function called by cURL for saving the response headers
    *
    * @param    resource    cURL handle
    * @param    string      response header (with trailing CRLF)
    * @return   integer     number of bytes saved
    * @see      HTTP_Request2_Response::parseHeaderLine()
    */
    protected function callbackWriteHeader($ch, $string)
    {
        // we may receive a second set of headers if doing e.g. digest auth
        if ($this->eventReceivedHeaders || !$this->eventSentHeaders) {
            // don't bother with 100-Continue responses (bug #15785)
            if (!$this->eventSentHeaders ||
                $this->response->getStatus() >= 200
            ) {
                $this->request->setLastEvent(
                    'sentHeaders', curl_getinfo($ch, CURLINFO_HEADER_OUT)
                );
            }
            $upload = curl_getinfo($ch, CURLINFO_SIZE_UPLOAD);
            // if body wasn't read by a callback, send event with total body size
            if ($upload > $this->position) {
                $this->request->setLastEvent(
                    'sentBodyPart', $upload - $this->position
                );
                $this->position = $upload;
            }
            if ($upload && (!$this->eventSentHeaders
                            || $this->response->getStatus() >= 200)
            ) {
                $this->request->setLastEvent('sentBody', $upload);
            }
            $this->eventSentHeaders = true;
            // we'll need a new response object
            if ($this->eventReceivedHeaders) {
                $this->eventReceivedHeaders = false;
                $this->response             = null;
            }
        }
        if (empty($this->response)) {
            $this->response = new HTTP_Request2_Response(
                $string, false, curl_getinfo($ch, CURLINFO_EFFECTIVE_URL)
            );
        } else {
            $this->response->parseHeaderLine($string);
            if ('' == trim($string)) {
                // don't bother with 100-Continue responses (bug #15785)
                if (200 <= $this->response->getStatus()) {
                    $this->request->setLastEvent('receivedHeaders', $this->response);
                }

                if ($this->request->getConfig('follow_redirects') && $this->response->isRedirect()) {
                    $redirectUrl = new Net_URL2($this->response->getHeader('location'));

                    // for versions lower than 5.2.10, check the redirection URL protocol
                    if (!defined('CURLOPT_REDIR_PROTOCOLS') && $redirectUrl->isAbsolute()
                        && !in_array($redirectUrl->getScheme(), array('http', 'https'))
                    ) {
                        return -1;
                    }

                    if ($jar = $this->request->getCookieJar()) {
                        $jar->addCookiesFromResponse($this->response, $this->request->getUrl());
                        if (!$redirectUrl->isAbsolute()) {
                            $redirectUrl = $this->request->getUrl()->resolve($redirectUrl);
                        }
                        if ($cookies = $jar->getMatching($redirectUrl, true)) {
                            curl_setopt($ch, CURLOPT_COOKIE, $cookies);
                        }
                    }
                }
                $this->eventReceivedHeaders = true;
            }
        }
        return strlen($string);
    }

   /**
    * Callback function called by cURL for saving the response body
    *
    * @param    resource    cURL handle (not used)
    * @param    string      part of the response body
    * @return   integer     number of bytes saved
    * @see      HTTP_Request2_Response::appendBody()
    */
    protected function callbackWriteBody($ch, $string)
    {
        // cURL calls WRITEFUNCTION callback without calling HEADERFUNCTION if
        // response doesn't start with proper HTTP status line (see bug #15716)
        if (empty($this->response)) {
            throw new HTTP_Request2_MessageException(
                "Malformed response: {$string}",
                HTTP_Request2_Exception::MALFORMED_RESPONSE
            );
        }
        if ($this->request->getConfig('store_body')) {
            $this->response->appendBody($string);
        }
        $this->request->setLastEvent('receivedBodyPart', $string);
        return strlen($string);
    }
}
?>
