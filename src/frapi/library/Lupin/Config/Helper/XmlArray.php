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
class Lupin_Config_Helper_XmlArray
{
    /**
     * Parse the xml file
     *
     * This method receives the name of an XML configuration file
     * then passes it to simplexml_load_file and it gets the
     * object variables using get_object_vars().
     *
     * Once the object is loaded, it passes it to the protected
     * local method $this->makeArray() which accepts a parameter
     * that can be either an object, an array or text.
     *
     * @param  string $xmlFile  The xml configuration file to parse.
     * @return array  An array of information coming from your configuration file.
     */
    public function parse($xmlFile)
    {
        $xml = get_object_vars(
            simplexml_load_file($xmlFile)
        );

        return $this->makeArray($xml);
    }

    /**
     * Make an array out of an XML object
     *
     * This method receive a SimplXMLElement object and make
     * an associative array out of it.
     *
     * @param  mixed $xml  The xml configuration to translate into an array
     * @return array An associative array of the configuration file.
     */
    protected function makeArray($xml)
    {
        if (!is_object($xml) && !is_array($xml)) {
            return $xml;
        }

        if (is_object($xml)) {
            $xml = get_object_vars($xml);
        }

        return array_map(array($this, 'makeArray'), $xml);
    }

    /**
     * Write an xml configuration
     *
     * Like it's alter-ego $this->parse() the write method is doing some xml to array
     * magic on the array passed to write.
     *
     * This method accepts one array parameter and this parameter should have at
     * least 2 keys. The first key is the name of the actual data of the configuration
     * and the second is the file in which the configuration must be written to.
     *
     * Ex:
     * <?php
     *     $params = array(
     *         'config'       => $arrayOfXmlConfigs,
     *         'configFile'   => $filenameOfTheConfig
     *         'topLevelName' => 'actions', // Lupin_Config_Xml::$configName
     *     );
     * ?>
     *
     * @throws Lupin_Config_Xml_Exception
     *
     * @param  array $config    The array of information related to the new
     *                          configuration settings to save/udpate.
     *
     * @return bool  true|false Depending on the success of the operation.
     */
    public function write(array $config)
    {
        if (!isset($config['config']) || !isset($config['filename']) ||
            !isset($config['topLevelName']))
        {
            throw new Lupin_Config_Xml_Exception('Missing config or filename or topLevelName');
        }

        $filename      = $config['filename'];
        $configArray   = $config['config'];
        $topLevelName  = $config['topLevelName'];

        $xml = $this->makeXml($topLevelName, $configArray);

        return file_put_contents($filename, $xml);
    }

    public function makeXml($topLevelName, array $configArray)
    {
        $xmlHelper = new Lupin_Config_Helper_XmlArrayWriter();

        $xmlHelper->startElement($topLevelName);

        $xml = $xmlHelper->setElementFromArray($xmlHelper->xmlObject, $topLevelName, $configArray);
        $xmlHelper->endElement();

        return $xmlHelper->getXml();
    }
}
