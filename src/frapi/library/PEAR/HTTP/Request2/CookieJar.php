<?php
/**
 * Stores cookies and passes them between HTTP requests
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
 * @version    SVN: $Id: CookieJar.php 308629 2011-02-24 17:34:24Z avb $
 * @link       http://pear.php.net/package/HTTP_Request2
 */

/** Class representing a HTTP request message */
require_once 'HTTP/Request2.php';

/**
 * Stores cookies and passes them between HTTP requests
 *
 * @category   HTTP
 * @package    HTTP_Request2
 * @author     Alexey Borzov <avb@php.net>
 * @version    Release: @package_version@
 */
class HTTP_Request2_CookieJar implements Serializable
{
   /**
    * Array of stored cookies
    *
    * The array is indexed by domain, path and cookie name
    *   .example.com
    *     /
    *       some_cookie => cookie data
    *     /subdir
    *       other_cookie => cookie data
    *   .example.org
    *     ...
    *
    * @var array
    */
    protected $cookies = array();

   /**
    * Whether session cookies should be serialized when serializing the jar
    * @var bool
    */
    protected $serializeSession = false;

   /**
    * Whether Public Suffix List should be used for domain matching
    * @var bool
    */
    protected $useList = true;

   /**
    * Array with Public Suffix List data
    * @var  array
    * @link http://publicsuffix.org/
    */
    protected static $psl = array();

   /**
    * Class constructor, sets various options
    *
    * @param bool Controls serializing session cookies, see {@link serializeSessionCookies()}
    * @param bool Controls using Public Suffix List, see {@link usePublicSuffixList()}
    */
    public function __construct($serializeSessionCookies = false, $usePublicSuffixList = true)
    {
        $this->serializeSessionCookies($serializeSessionCookies);
        $this->usePublicSuffixList($usePublicSuffixList);
    }

   /**
    * Returns current time formatted in ISO-8601 at UTC timezone
    *
    * @return string
    */
    protected function now()
    {
        $dt = new DateTime();
        $dt->setTimezone(new DateTimeZone('UTC'));
        return $dt->format(DateTime::ISO8601);
    }

   /**
    * Checks cookie array for correctness, possibly updating its 'domain', 'path' and 'expires' fields
    *
    * The checks are as follows:
    *   - cookie array should contain 'name' and 'value' fields;
    *   - name and value should not contain disallowed symbols;
    *   - 'expires' should be either empty parseable by DateTime;
    *   - 'domain' and 'path' should be either not empty or an URL where
    *     cookie was set should be provided.
    *   - if $setter is provided, then document at that URL should be allowed
    *     to set a cookie for that 'domain'. If $setter is not provided,
    *     then no domain checks will be made.
    *
    * 'expires' field will be converted to ISO8601 format from COOKIE format,
    * 'domain' and 'path' will be set from setter URL if empty.
    *
    * @param    array    cookie data, as returned by {@link HTTP_Request2_Response::getCookies()}
    * @param    Net_URL2 URL of the document that sent Set-Cookie header
    * @return   array    Updated cookie array
    * @throws   HTTP_Request2_LogicException
    * @throws   HTTP_Request2_MessageException
    */
    protected function checkAndUpdateFields(array $cookie, Net_URL2 $setter = null)
    {
        if ($missing = array_diff(array('name', 'value'), array_keys($cookie))) {
            throw new HTTP_Request2_LogicException(
                "Cookie array should contain 'name' and 'value' fields",
                HTTP_Request2_Exception::MISSING_VALUE
            );
        }
        if (preg_match(HTTP_Request2::REGEXP_INVALID_COOKIE, $cookie['name'])) {
            throw new HTTP_Request2_LogicException(
                "Invalid cookie name: '{$cookie['name']}'",
                HTTP_Request2_Exception::INVALID_ARGUMENT
            );
        }
        if (preg_match(HTTP_Request2::REGEXP_INVALID_COOKIE, $cookie['value'])) {
            throw new HTTP_Request2_LogicException(
                "Invalid cookie value: '{$cookie['value']}'",
                HTTP_Request2_Exception::INVALID_ARGUMENT
            );
        }
        $cookie += array('domain' => '', 'path' => '', 'expires' => null, 'secure' => false);

        // Need ISO-8601 date @ UTC timezone
        if (!empty($cookie['expires'])
            && !preg_match('/^\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}\\+0000$/', $cookie['expires'])
        ) {
            try {
                $dt = new DateTime($cookie['expires']);
                $dt->setTimezone(new DateTimeZone('UTC'));
                $cookie['expires'] = $dt->format(DateTime::ISO8601);
            } catch (Exception $e) {
                throw new HTTP_Request2_LogicException($e->getMessage());
            }
        }

        if (empty($cookie['domain']) || empty($cookie['path'])) {
            if (!$setter) {
                throw new HTTP_Request2_LogicException(
                    'Cookie misses domain and/or path component, cookie setter URL needed',
                    HTTP_Request2_Exception::MISSING_VALUE
                );
            }
            if (empty($cookie['domain'])) {
                if ($host = $setter->getHost()) {
                    $cookie['domain'] = $host;
                } else {
                    throw new HTTP_Request2_LogicException(
                        'Setter URL does not contain host part, can\'t set cookie domain',
                        HTTP_Request2_Exception::MISSING_VALUE
                    );
                }
            }
            if (empty($cookie['path'])) {
                $path = $setter->getPath();
                $cookie['path'] = empty($path)? '/': substr($path, 0, strrpos($path, '/') + 1);
            }
        }

        if ($setter && !$this->domainMatch($setter->getHost(), $cookie['domain'])) {
            throw new HTTP_Request2_MessageException(
                "Domain " . $setter->getHost() . " cannot set cookies for "
                . $cookie['domain']
            );
        }

        return $cookie;
    }

   /**
    * Stores a cookie in the jar
    *
    * @param    array    cookie data, as returned by {@link HTTP_Request2_Response::getCookies()}
    * @param    Net_URL2 URL of the document that sent Set-Cookie header
    * @throws   HTTP_Request2_Exception
    */
    public function store(array $cookie, Net_URL2 $setter = null)
    {
        $cookie = $this->checkAndUpdateFields($cookie, $setter);

        if (strlen($cookie['value'])
            && (is_null($cookie['expires']) || $cookie['expires'] > $this->now())
        ) {
            if (!isset($this->cookies[$cookie['domain']])) {
                $this->cookies[$cookie['domain']] = array();
            }
            if (!isset($this->cookies[$cookie['domain']][$cookie['path']])) {
                $this->cookies[$cookie['domain']][$cookie['path']] = array();
            }
            $this->cookies[$cookie['domain']][$cookie['path']][$cookie['name']] = $cookie;

        } elseif (isset($this->cookies[$cookie['domain']][$cookie['path']][$cookie['name']])) {
            unset($this->cookies[$cookie['domain']][$cookie['path']][$cookie['name']]);
        }
    }

   /**
    * Adds cookies set in HTTP response to the jar
    *
    * @param HTTP_Request2_Response response
    * @param Net_URL2               original request URL, needed for setting
    *                               default domain/path
    */
    public function addCookiesFromResponse(HTTP_Request2_Response $response, Net_URL2 $setter)
    {
        foreach ($response->getCookies() as $cookie) {
            $this->store($cookie, $setter);
        }
    }

   /**
    * Returns all cookies matching a given request URL
    *
    * The following checks are made:
    *   - cookie domain should match request host
    *   - cookie path should be a prefix for request path
    *   - 'secure' cookies will only be sent for HTTPS requests
    *
    * @param  Net_URL2
    * @param  bool      Whether to return cookies as string for "Cookie: " header
    * @return array
    */
    public function getMatching(Net_URL2 $url, $asString = false)
    {
        $host   = $url->getHost();
        $path   = $url->getPath();
        $secure = 0 == strcasecmp($url->getScheme(), 'https');

        $matched = $ret = array();
        foreach (array_keys($this->cookies) as $domain) {
            if ($this->domainMatch($host, $domain)) {
                foreach (array_keys($this->cookies[$domain]) as $cPath) {
                    if (0 === strpos($path, $cPath)) {
                        foreach ($this->cookies[$domain][$cPath] as $name => $cookie) {
                            if (!$cookie['secure'] || $secure) {
                                $matched[$name][strlen($cookie['path'])] = $cookie;
                            }
                        }
                    }
                }
            }
        }
        foreach ($matched as $cookies) {
            krsort($cookies);
            $ret = array_merge($ret, $cookies);
        }
        if (!$asString) {
            return $ret;
        } else {
            $str = '';
            foreach ($ret as $c) {
                $str .= (empty($str)? '': '; ') . $c['name'] . '=' . $c['value'];
            }
            return $str;
        }
    }

   /**
    * Returns all cookies stored in a jar
    *
    * @return array
    */
    public function getAll()
    {
        $cookies = array();
        foreach (array_keys($this->cookies) as $domain) {
            foreach (array_keys($this->cookies[$domain]) as $path) {
                foreach ($this->cookies[$domain][$path] as $name => $cookie) {
                    $cookies[] = $cookie;
                }
            }
        }
        return $cookies;
    }

   /**
    * Sets whether session cookies should be serialized when serializing the jar
    *
    * @param    boolean
    */
    public function serializeSessionCookies($serialize)
    {
        $this->serializeSession = (bool)$serialize;
    }

   /**
    * Sets whether Public Suffix List should be used for restricting cookie-setting
    *
    * Without PSL {@link domainMatch()} will only prevent setting cookies for
    * top-level domains like '.com' or '.org'. However, it will not prevent
    * setting a cookie for '.co.uk' even though only third-level registrations
    * are possible in .uk domain.
    *
    * With the List it is possible to find the highest level at which a domain
    * may be registered for a particular top-level domain and consequently
    * prevent cookies set for '.co.uk' or '.msk.ru'. The same list is used by
    * Firefox, Chrome and Opera browsers to restrict cookie setting.
    *
    * Note that PSL is licensed differently to HTTP_Request2 package (refer to
    * the license information in public-suffix-list.php), so you can disable
    * its use if this is an issue for you.
    *
    * @param    boolean
    * @link     http://publicsuffix.org/learn/
    */
    public function usePublicSuffixList($useList)
    {
        $this->useList = (bool)$useList;
    }

   /**
    * Returns string representation of object
    *
    * @return string
    * @see    Serializable::serialize()
    */
    public function serialize()
    {
        $cookies = $this->getAll();
        if (!$this->serializeSession) {
            for ($i = count($cookies) - 1; $i >= 0; $i--) {
                if (empty($cookies[$i]['expires'])) {
                    unset($cookies[$i]);
                }
            }
        }
        return serialize(array(
            'cookies'          => $cookies,
            'serializeSession' => $this->serializeSession,
            'useList'          => $this->useList
        ));
    }

   /**
    * Constructs the object from serialized string
    *
    * @param string  string representation
    * @see   Serializable::unserialize()
    */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $now  = $this->now();
        $this->serializeSessionCookies($data['serializeSession']);
        $this->usePublicSuffixList($data['useList']);
        foreach ($data['cookies'] as $cookie) {
            if (!empty($cookie['expires']) && $cookie['expires'] <= $now) {
                continue;
            }
            if (!isset($this->cookies[$cookie['domain']])) {
                $this->cookies[$cookie['domain']] = array();
            }
            if (!isset($this->cookies[$cookie['domain']][$cookie['path']])) {
                $this->cookies[$cookie['domain']][$cookie['path']] = array();
            }
            $this->cookies[$cookie['domain']][$cookie['path']][$cookie['name']] = $cookie;
        }
    }

   /**
    * Checks whether a cookie domain matches a request host.
    *
    * The method is used by {@link store()} to check for whether a document
    * at given URL can set a cookie with a given domain attribute and by
    * {@link getMatching()} to find cookies matching the request URL.
    *
    * @param    string  request host
    * @param    string  cookie domain
    * @return   bool    match success
    */
    public function domainMatch($requestHost, $cookieDomain)
    {
        if ($requestHost == $cookieDomain) {
            return true;
        }
        // IP address, we require exact match
        if (preg_match('/^(?:\d{1,3}\.){3}\d{1,3}$/', $requestHost)) {
            return false;
        }
        if ('.' != $cookieDomain[0]) {
            $cookieDomain = '.' . $cookieDomain;
        }
        // prevents setting cookies for '.com' and similar domains
        if (!$this->useList && substr_count($cookieDomain, '.') < 2
            || $this->useList && !self::getRegisteredDomain($cookieDomain)
        ) {
            return false;
        }
        return substr('.' . $requestHost, -strlen($cookieDomain)) == $cookieDomain;
    }

   /**
    * Removes subdomains to get the registered domain (the first after top-level)
    *
    * The method will check Public Suffix List to find out where top-level
    * domain ends and registered domain starts. It will remove domain parts
    * to the left of registered one.
    *
    * @param  string        domain name
    * @return string|bool   registered domain, will return false if $domain is
    *                       either invalid or a TLD itself
    */
    public static function getRegisteredDomain($domain)
    {
        $domainParts = explode('.', ltrim($domain, '.'));

        // load the list if needed
        if (empty(self::$psl)) {
            $path = '@data_dir@' . DIRECTORY_SEPARATOR . 'HTTP_Request2';
            if (0 === strpos($path, '@' . 'data_dir@')) {
                $path = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
                                 . DIRECTORY_SEPARATOR . 'data');
            }
            self::$psl = include_once $path . DIRECTORY_SEPARATOR . 'public-suffix-list.php';
        }

        if (!($result = self::checkDomainsList($domainParts, self::$psl))) {
            // known TLD, invalid domain name
            return false;
        }

        // unknown TLD
        if (!strpos($result, '.')) {
            // fallback to checking that domain "has at least two dots"
            if (2 > ($count = count($domainParts))) {
                return false;
            }
            return $domainParts[$count - 2] . '.' . $domainParts[$count - 1];
        }
        return $result;
    }

   /**
    * Recursive helper method for {@link getRegisteredDomain()}
    *
    * @param  array         remaining domain parts
    * @param  mixed         node in {@link HTTP_Request2_CookieJar::$psl} to check
    * @return string|null   concatenated domain parts, null in case of error
    */
    protected static function checkDomainsList(array $domainParts, $listNode)
    {
        $sub    = array_pop($domainParts);
        $result = null;

        if (!is_array($listNode) || is_null($sub)
            || array_key_exists('!' . $sub, $listNode)
         ) {
            return $sub;

        } elseif (array_key_exists($sub, $listNode)) {
            $result = self::checkDomainsList($domainParts, $listNode[$sub]);

        } elseif (array_key_exists('*', $listNode)) {
            $result = self::checkDomainsList($domainParts, $listNode['*']);

        } else {
            return $sub;
        }

        return (strlen($result) > 0) ? ($result . '.' . $sub) : null;
    }
}
?>