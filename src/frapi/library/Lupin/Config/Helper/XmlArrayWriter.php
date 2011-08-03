<?php
/**
 * XmlArray Helper
 *
 * This class is used by the Lupin_Config_Xml class to fetch an XML configuration
 * file and return it as an array.
 *
 * This class is also used to receive an array and make an XML file out of it.
 *
 * @todo implement @writer
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
 * @package   frapi
 */
class Lupin_Config_Helper_XmlArrayWriter
{
    public $xmlObject = null;

    public function __construct()
    {

        $this->xmlObject = new XMLWriter();
        $this->xmlObject->openMemory();
        $this->xmlObject->setIndent(true);
        $this->xmlObject->setIndentString(' ');
        $this->xmlObject->startDocument('1.0', 'UTF-8');
        $this->xmlObject->startElement('frapi-config');
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->xmlObject, $method), $args);
    }

    public function setElementFromArray(XMLWriter $xml, $rootNode, array $config)
    {
        $config = $this->normalize($config);

        if (!empty($config)) {
            foreach ($config as $key => $val) {
                $numeric = 0;
                if (is_numeric($key)) {
                    $numeric = 1;
                    $key     = $rootNode;
                }

                if (is_array($val)) {
                    $isAssoc = $this->isAssoc($val);
                    if ($isAssoc || $numeric) {
                        $xml->startElement($key);
                    }

                    $this->setElementFromArray($xml, $key, $val);

                    if ($isAssoc || $numeric) {
                        $xml->endElement();
                    }

                    continue;
                }

                $xml->writeElement($key, $val);
            }
        }
    }

    private function normalize(array $array)
    {
        $ret = array();
        foreach ($array as $key => $val) {
            if (is_string($val)) {
                $ret[$key] = $val;
                unset($array[$key]);
            }
        }

        // This is the minized loop with only non-string vals.
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $ret[$key] = $val;
            }
        }

        return $ret;
    }

    public function getXml()
    {
        $this->xmlObject->endElement();
        $this->xmlObject->endDocument();
        return $this->xmlObject->outputMemory();
    }

    public function isAssoc ($array)
    {
        $array = (is_array($array)) ? array_merge(array(), $array) : $array;
        return (
            is_array($array) &&
            count(
                array_diff_key($array, array_keys(array_keys($array)))
            ) > 0
        );
    }
}
