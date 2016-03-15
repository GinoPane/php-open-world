<?php

iconv_set_encoding('input_encoding', 'UTF-8');
iconv_set_encoding('internal_encoding', 'UTF-8');
iconv_set_encoding('output_encoding', 'UTF-8');

/**
 *
 * Service class for XML handling, converts xml to arrays and arrays to xml
 * @see XmlWrapper::arrayToXml, XmlWrapper::xmlToArray
 *
 *
 * @author Sergey Karavay
 *
 */
class XmlWrapper
{
    private $_xml = null;
    private $_encoding = 'UTF-8';
    private $_options = array();

    private function __construct($options = array())
    {
        $version = "1.0";
        $formatOutput = true;
        $encoding = 'UTF-8';

        extract($options, EXTR_IF_EXISTS | EXTR_OVERWRITE);

        $this->_xml = new DomDocument($version, $encoding);

        $this->_options = $options;

        $this->_xml->formatOutput = $formatOutput;

        $this->_encoding = $encoding;
    }

    /**
     *
     *
     * @param array $options Options for parser;
     *  - 'version'
     *  - 'formatOutput'
     *  - 'encoding'
     *  - 'numericKeysName'
     *
     * @return XmlWrapper
     */
    public static function getParser($options = array())
    {
        return new XmlWrapper($options);
    }

    /**
     * Converts an Array to XML
     *
     * @param array $data array to be converted
     * @param array $options array of options. Will be merged with existing.
     *
     * @see XmlWrapper::getParser() for more
     * @return DomDocument
     */
    public function arrayToXml(array $data, $options = array())
    {
        $xml = $this->_getXMLRoot();

        $rootNodeName = 'root';

        extract($options, EXTR_IF_EXISTS | EXTR_OVERWRITE);

        $this->_options = array_merge($this->_options, $options);

        $xml->appendChild($this->_convertArrayToXml($rootNodeName, $data));

        return $xml->saveXML();
    }

    /**
     *
     * Converts xml string to array
     *
     * @param mixed $inputXml DOMDocument instance or a valid xml string
     * @return array converted array
     */
    public function xmlToArray($inputXml)
    {
        $xml = $this->_getXMLRoot();

        $error = false;

        if (is_string($inputXml)) {
            if (is_file($inputXml) && is_readable($inputXml)) {
                if (!$xml->load($inputXml)) {
                    trigger_error('Error parsing the XML file.', E_USER_WARNING);
                    $error = true;
                }
            } elseif (!$xml->loadXML($inputXml)) {
                trigger_error('Error parsing the XML string.', E_USER_WARNING);
                $error = true;
            }
        } else {
            if (is_a($inputXml, 'DOMDocument')) {
                trigger_error('The input XML object should be descendant of DOMDocument', E_USER_WARNING);
                $error = true;
            }

            $xml = $this->_xml = $inputXml;
        }

        if (!$error) {
            $output = array();

            $output[$xml->documentElement->tagName] =
                $this->_convertXmlToArray($xml->documentElement);

            return $output;
        } else {
            return null;
        }
    }

    /**
     * Convert an Array to XML
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DOMNode
     */
    private function _convertArrayToXml($nodeName, $arr = array())
    {
        $node = $this->_xml->createElement($nodeName);

        if (is_array($arr)) {
            if (isset($arr['@attributes'])) {
                foreach ($arr['@attributes'] as $key => $value) {
                    if (!$this->_isValidTagName($key)) {
                        trigger_error("Illegal attribute name: \"{$key}\" in node \"{$nodeName}\"");
                    }
                    $node->setAttribute($key, self::_valueToString($value));
                }
                unset($arr['@attributes']);
            }

            // check if it has a value stored in @value, if yes store the value and return
            // else check if its directly stored as string
            if (isset($arr['@value'])) {
                $node->appendChild($this->_xml->createTextNode($this->_valueToString($arr['@value'])));
                unset($arr['@value']);
                return $node;
            } else if (isset($arr['@cdata'])) {
                $node->appendChild($this->_xml->createCDATASection($this->_valueToString($arr['@cdata'])));
                unset($arr['@cdata']);
                return $node;
            }
        }

        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                if (!$this->_isValidTagName($key)) {
                    trigger_error("Illegal tag name: \"{$key}\" in node \"{$nodeName}\"");
                }
                if (is_array($value) && is_numeric(key($value))) {
                    $numericKeyName = isset($this->_options['numericKeysName']) ? $this->_options['numericKeysName'] : $key;
                    foreach ($value as $subValue) {
                        $node->appendChild($this->_convertArrayToXml(
                            $numericKeyName,
                            $subValue
                        ));
                    }
                } else {
                    $node->appendChild($this->_convertArrayToXml($key, $value));
                }
                unset($arr[$key]);
            }
        }

        if (!is_array($arr)) {
            $node->appendChild($this->_xml->createTextNode($this->_valueToString($arr)));
        }

        return $node;
    }

    /**
     * Convert an XML to array
     *
     * @param DOMNode $node
     * @return array
     */
    private function _convertXmlToArray(DOMNode $node)
    {
        $output = array();

        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
                $output['@cdata'] = trim($node->textContent);
                break;

            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;

            case XML_ELEMENT_NODE:
                // for each child node, call the covert function recursively
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->_convertXmlToArray($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;

                        // assume more nodes of same kind are coming
                        if (!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } else {
                        //check if it is not an empty text node
                        if ($v !== '') {
                            $output = $v;
                        }
                    }
                }

                if (is_array($output)) {
                    // if only one node of its kind, assign it directly instead if array($value);
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1) {
                            $output[$t] = $v[0];
                        }
                    }
                    if (empty($output)) {
                        //for empty nodes
                        $output = '';
                    }
                }

                // loop through the attributes and collect them
                if ($node->attributes->length) {
                    $a = array();
                    foreach ($node->attributes as $attrName => $attrNode) {
                        $a[$attrName] = (string)$attrNode->value;
                    }
                    // if its an leaf node, store the value in @value instead of directly storing it.
                    if (!is_array($output)) {
                        $output = array('@value' => $output);
                    }
                    $output['@attributes'] = $a;
                }
                break;

            default:
                $output = '';
        }

        return $output;
    }

    private function _getXMLRoot()
    {
        if (!$this->_xml) {
            $this->_xml = new DOMDocument();
        }

        return $this->_xml;
    }

    /*
     * Get string representation of the value
     */
    private function _valueToString($value)
    {
        if (!is_bool($value)) {
            return (string)$value;
        } else {
            return $value ? 'true' : 'false';
        }
    }

    /**
     * Check if the tag name or attribute name contains illegal characters
     * @link http://www.w3.org/TR/xml/#sec-common-syn
     *
     * @param string $tag
     * @return bool
     */

    private function _isValidTagName($tag)
    {
        $matches = array();
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';
        return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }
}

/**
 * show a status bar in the console
 *
 * <code>
 * for($x=1;$x<=100;$x++){
 *
 *     showStatus($x, 100);
 *
 *     usleep(100000);
 *
 * }
 * </code>
 *
 * @param   int $done how many items are completed
 * @param   int $total how many items are to be done total
 * @param   int $size optional size of the status bar
 * @return  void
 *
 */
function showStatus($done, $total, $size = 30)
{
    static $startTime;

    // if we go over our bound, just ignore it
    if ($done > $total) return;

    if (empty($startTime)) $startTime = time();
    $now = time();

    $percent = (double)($done / $total);

    $bar = floor($percent * $size);

    $statusBar = "\r[";
    $statusBar .= str_repeat("=", $bar);
    if ($bar < $size) {
        $statusBar .= ">";
        $statusBar .= str_repeat(" ", $size - $bar);
    } else {
        $statusBar .= "=";
    }

    $display = number_format($percent * 100, 0);

    $statusBar .= "] $display%  $done/$total";

    $rate = ($now - $startTime) / $done;
    $left = $total - $done;
    $eta = round($rate * $left, 2);

    $elapsed = $now - $startTime;

    $statusBar .= " remaining: " . number_format($eta) . " sec.  elapsed: " . number_format($elapsed) . " sec.";

    echo "$statusBar  ";

    flush();

    // when done, send a newline
    if ($done == $total) {
        echo "\n";
    }
}

function handleError($errno, $errstr, $errfile, $errline)
{
    if ($errno == E_NOTICE || $errno == E_WARNING) {
        throw new Exception("$errstr in $errfile @ line $errline \n", $errno);
    }
}

function handleCldrCheckoutError($directory, $code, $output)
{
    if ($code === 0) {
        if (!is_dir($directory)) {
            $code = -1;
        }
    }
    if ($code !== 0) {
        $msg = "Error!\n";
        if (stripos(PHP_OS, 'WIN') !== false) {
            $msg .= 'Please make sure that SVN is installed and in your path. You can install TortoiseSVN for instance.';
        } else {
            $msg .= "You need the svn command line tool: under Ubuntu and Debian systems you can for instance run 'sudo apt-get install subversion'";
        }
        $msg .= "\nError details:\n" . implode("\n", $output);
        throw new Exception($msg);
    }
}

function checkFileExistence($fileName)
{
    echo "Checking \"$fileName\"...\n";

    if (!is_readable($fileName)) {
        throw new Exception("$fileName is not found or is not readable! \n");
    }

    echo "File is available. Processing...\n";

    return true;
}

function checkoutCLDR()
{
    if (file_exists(LOCAL_VCS_DIR)) {
        deleteFromFilesystem(LOCAL_VCS_DIR);
    }

    try {
        $dirs = array(
            'common/main' => 'main',
            'common/supplemental' => 'supplemental',
        );

        foreach ($dirs as $source => $target) {
            echo "Checking out the CLDR $target repository (this may take a while)... \n";

            $output = array();
            $rc = null;

            $directory = LOCAL_VCS_DIR . DIRECTORY_SEPARATOR . $target;

            if (mkdir($directory, 0777, true) === false) {
                echo 'Failed to create ' . $directory . "\n";
                die(1);
            }

            @exec('svn co http://www.unicode.org/repos/cldr/tags/release-' . CLDR_VERSION . '/' . $source . ' ' . escapeshellarg($directory) . ' 2>&1', $output, $rc);

            handleCldrCheckoutError($directory, $rc, $output);
        }

        echo "done.\n";
    } catch (Exception $x) {
        if (file_exists(LOCAL_VCS_DIR)) {
            try {
                deleteFromFilesystem(LOCAL_VCS_DIR);
            } catch (Exception $foo) {
            }
        }
        throw $x;
    }
}

/*
 * Build specific data for locales (every or only most popular)
 */
function buildLocaleSpecificData()
{
    die();
    echo 'Determining the list of the available locales... ';
    $availableLocales = array();
    $contents = @scandir(LOCAL_VCS_DIR . DIRECTORY_SEPARATOR . 'main');

    if ($contents === false) {
        throw new Exception('Error reading contents of the directory ' . LOCAL_VCS_DIR . '/main');
    }

    $match = null;

    foreach ($contents as $item) {
        if (preg_match('/^(.+)\.xml$/', $item, $match)) {
            $availableLocales[] = str_replace('_', '-', $match[1]);
        }
    }

    if (empty($availableLocales)) {
        throw new Exception('No locales found!');
    }

    sort($availableLocales);

    echo count($availableLocales) . " locales found.\n";

    if (FULL_JSON) {
        $locales = $availableLocales;
    } else {
        echo "Checking standard locales... \n";
        // Same locales as of CLDR 26 not-full distribution
        $locales = array('ar', 'ca', 'cs', 'da', 'de', 'el', 'en', 'en-001', 'en-AU', 'en-CA', 'en-GB', 'en-HK', 'en-IN', 'es', 'fi', 'fr', 'he', 'hi', 'hr', 'hu', 'it', 'ja', 'ko', 'nb', 'nl', 'nn', 'pl', 'pt', 'pt-PT', 'ro', 'root', 'ru', 'sk', 'sl', 'sr', 'sv', 'th', 'tr', 'uk', 'vi', 'zh', 'zh-Hant');
        $diff = array_diff($locales, $availableLocales);
        if (!empty($diff)) {
            throw new Exception("The following locales were not found:\n- " . implode("\n- ", $diff));
        }
        echo "Done.\n";
    }

    foreach ($locales as $locale) {
        echo "Building json data for $locale... \n";

        $cmd = 'java';
        $cmd .= ' -DCLDR_DIR=' . escapeshellarg(LOCAL_VCS_DIR);
        $cmd .= ' -DCLDR_GEN_DIR=' . escapeshellarg(SOURCE_DIR_DATA . '/main/' . $locale);
        $cmd .= ' -jar ' . escapeshellarg(LOCAL_VCS_DIR . '/tools/java/cldr.jar');
        $cmd .= ' ldml2json';
        $cmd .= ' -o true'; // (true|false) Whether to write out the 'other' section, which contains any unmatched paths
        $cmd .= ' -t main'; // (main|supplemental|segments) Type of CLDR data being generated, main, supplemental, or segments.
        $cmd .= ' -r true'; // (true|false) Whether the output JSON for the main directory should be based on resolved or unresolved data
        $cmd .= ' -m ' . escapeshellarg(str_replace('-', '_', $locale)); // Regular expression to define only specific locales or files to be generated
        $output = array();
        $rc = null;
        @exec($cmd . ' 2>&1', $output, $rc);
        if ($rc !== 0) {
            throw new Exception("Error!\n" . implode("\n", $output));
        }
        if (!is_dir(SOURCE_DIR_DATA . '/main/' . $locale)) {
            throw new Exception("No data generated!\nTool output:\n" . implode("\n", $output));
        }
        echo "Done.\n";
    }
}

/*
 * Build supplemental data for CLDR
 */
function buildSupplementalData()
{
    echo "Building supplemental data... \n";

    /*
     * Process supplementalData.xml
     */
    $supplementalDataFile = LOCAL_VCS_DIR . str_replace("/", DIRECTORY_SEPARATOR, "/supplemental/supplementalData.xml");

    if (checkFileExistence($supplementalDataFile)) {

    }

    die();
    $result = XmlWrapper::getParser()->xmlToArray(LOCAL_VCS_DIR .
        str_replace("/", DIRECTORY_SEPARATOR, "/supplemental/supplementalData.xml"));
    print_r(array_keys($result['supplementalData']['currencyData']));

    //file_put_contents("$locale.json", json_encode($result['ldml']['numbers']['currencies']['currency'], JSON_UNESCAPED_UNICODE));
}

function buildCLDRJson()
{
    try {

        buildSupplementalData();

        buildLocaleSpecificData();

    } catch (Exception $x) {
        try {
            deleteFromFilesystem(SOURCE_DIR_DATA);
        } catch (Exception $foo) {
        }
        throw $x;
    }
}

function copyData()
{
    $copy = array(
        'ca-gregorian.json' => array('kind' => 'main', 'save-as' => 'calendar.json', 'roots' => array('dates', 'calendars', 'gregorian')),
        'timeZoneNames.json' => array('kind' => 'main', 'roots' => array('dates', 'timeZoneNames')),
        'listPatterns.json' => array('kind' => 'main', 'roots' => array('listPatterns')),
        'units.json' => array('kind' => 'main', 'roots' => array('units')),
        'dateFields.json' => array('kind' => 'main', 'roots' => array('dates', 'fields')),
        'languages.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames', 'languages')),
        'territories.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames', 'territories')),
        'localeDisplayNames.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames')),
        'numbers.json' => array('kind' => 'main', 'roots' => array('numbers')),
        'layout.json' => array('kind' => 'main', 'roots' => array('layout', 'orientation')),
        'measurementSystemNames.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames', 'measurementSystemNames')),
        'currencies.json' => array('kind' => 'main', 'roots' => array('numbers', 'currencies')),
        /*
        'characters.json' => array('kind' => 'main', 'roots' => array('characters')),
        'contextTransforms.json' => array('kind' => 'main', 'roots' => array('contextTransforms')),

        'delimiters.json' => array('kind' => 'main', 'roots' => array('delimiters')),
        'scripts.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames', 'scripts')),
        'transformNames.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames', 'transformNames')),
        'variants.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames', 'variants')),
        */
        'telephoneCodeData.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'telephoneCodeData')),
        'territoryInfo.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'territoryInfo')),
        'weekData.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'weekData')),
        'parentLocales.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'parentLocales', 'parentLocale')),
        'likelySubtags.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'likelySubtags')),
        'territoryContainment.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'territoryContainment')),
        'metaZones.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'metaZones')),
        'plurals.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'plurals-type-cardinal')),
        'measurementData.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'measurementData')),
        'currencyData.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'currencyData')),
    );
    $src = SOURCE_DIR_DATA . DIRECTORY_SEPARATOR . 'main';
    $locales = scandir($src);
    if ($locales === false) {
        throw new Exception("Failed to retrieve the file list of $src");
    }
    $locales = array_diff($locales, array('.', '..', 'en-001'));
    foreach ($locales as $locale) {
        if (is_dir($src . DIRECTORY_SEPARATOR . $locale)) {
            echo "Parsing locale $locale... ";
            $destFolder = DESTINATION_DIR . DIRECTORY_SEPARATOR . $locale;
            if (is_dir($destFolder)) {
                deleteFromFilesystem($destFolder);
            }
            if (mkdir($destFolder) === false) {
                throw new Exception("Failed to create $destFolder\n");
            }
            foreach ($copy as $copyFrom => $info) {
                if ($info['kind'] === 'main') {
                    $copyTo = array_key_exists('save-as', $info) ? $info['save-as'] : $copyFrom;
                    if ($copyTo === false) {
                        $copyTo = $copyFrom;
                    }
                    $dstFile = $destFolder . DIRECTORY_SEPARATOR . $copyTo;
                    $useLocale = $locale;
                    $srcFile = $src . DIRECTORY_SEPARATOR . $useLocale . DIRECTORY_SEPARATOR . $copyFrom;
                    if (!is_file($srcFile)) {
                        $useLocale = 'en';
                        $srcFile = $src . DIRECTORY_SEPARATOR . $useLocale . DIRECTORY_SEPARATOR . $copyFrom;
                        if (!is_file($srcFile)) {
                            throw new Exception("File not found: $srcFile");
                        }
                    }
                    $info['roots'] = array_merge(array('main', $useLocale), $info['roots']);
                    $info['unsetByPath'] = array_merge(
                        isset($info['unsetByPath']) ? $info['unsetByPath'] : array(),
                        array(
                            "/main/$useLocale" => array('identity'),
                        )
                    );
                    copyDataFile($srcFile, $info, $dstFile);
                }
            }
            echo "Done.\n";
        }
    }
    $defaultCurrencyData = readJsonFile(DESTINATION_DIR . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . 'currencies.json');
    foreach ($locales as $locale) {
        if ($locale !== 'en') {
            if (is_dir($src . DIRECTORY_SEPARATOR . $locale)) {
                copyMissingData_currency($defaultCurrencyData, DESTINATION_DIR . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'currencies.json');
            }
        }
    }
    echo 'Parsing supplemental files... ';
    $src = SOURCE_DIR_DATA . DIRECTORY_SEPARATOR . 'supplemental';
    foreach ($copy as $copyFrom => $info) {
        if ($info['kind'] === 'supplemental') {
            $copyTo = array_key_exists('save-as', $info) ? $info['save-as'] : $copyFrom;
            $dstFile = DESTINATION_DIR . DIRECTORY_SEPARATOR . $copyTo;
            $srcFile = $src . DIRECTORY_SEPARATOR . $copyFrom;
            if (!is_file($srcFile)) {
                throw new Exception("File not found: $srcFile");
            }
            $info['unsetByPath'] = array_merge(
                isset($info['unsetByPath']) ? $info['unsetByPath'] : array(),
                array(
                    '/supplemental' => array('version', 'generation'),
                )
            );
            copyDataFile($srcFile, $info, $dstFile);
        }
    }
    echo "Done.\n";
}

function readJsonFile($file)
{
    $json = file_get_contents($file);
    if ($json === false) {
        throw new Exception("Failed to read from $file");
    }
    $data = json_decode($json, true);
    if ($data === null) {
        throw new Exception("Failed to decode data in $file");
    }

    return $data;
}

function saveJsonFile($data, $file)
{
    $jsonFlags = 0;
    if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
        $jsonFlags |= JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        if (DEBUG) {
            $jsonFlags |= JSON_PRETTY_PRINT;
        }
    }
    $json = json_encode($data, $jsonFlags);
    if ($json === false) {
        throw new Exception("Failed to serialize data for $file");
    }
    if (is_file($file)) {
        deleteFromFilesystem($file);
    }
    if (file_put_contents($file, $json) === false) {
        throw new Exception("Failed write to $file");
    }
}

function deleteFromFilesystem($path)
{
    if (is_file($path)) {
        if (unlink($path) === false) {
            throw new Exception("Failed to delete file $path");
        }
    } else {
        $contents = scandir($path);
        if ($contents === false) {
            throw new Exception("Failed to retrieve the file list of $path");
        }
        foreach (array_diff($contents, array('.', '..')) as $item) {
            deleteFromFilesystem($path . DIRECTORY_SEPARATOR . $item);
        }
        if (rmdir($path) === false) {
            throw new Exception("Failed to delete directory $path");
        }
    }
}

function toPhpSprintf($fmt)
{
    $result = $fmt;
    if (is_string($fmt)) {
        $result = str_replace('%', '%%', $result);
        $result = preg_replace_callback(
            '/\\{(\\d+)\\}/',
            function ($matches) {
                return '%' . (1 + intval($matches[1])) . '$s';
            },
            $fmt
        );
    }

    return $result;
}

function fixMetazoneInfo($a)
{
    checkOneKey($a, 'usesMetazone');
    $a = $a['usesMetazone'];
    foreach (array_keys($a) as $key) {
        switch ($key) {
            case '_mzone':
            case '_from':
            case '_to':
                $a[substr($key, 1)] = $a[$key];
                unset($a[$key]);
                break;
            default:
                throw new Exception('Invalid metazoneInfo node');
        }
    }

    return $a;
}

function checkOneKey($node, $key)
{
    if (!is_array($node)) {
        throw new Exception("$node is not an array");
    }
    if (count($node) !== 1) {
        throw new Exception("Expected just one node '$key', found these keys: " . implode(', ', array_keys($node)));
    }
    if (!array_key_exists($key, $node)) {
        throw new Exception("Expected just one node '$key', found this key: " . implode(', ', array_keys($node)));
    }
}

function numberFormatToRegularExpressions($symbols, $isoPattern)
{
    $p = explode(';', $isoPattern);
    $patterns = array(
        '+' => $p[0],
        '-' => (count($p) == 1) ? "-{$p[0]}" : $p[1],
    );
    $result = array();
    $m = null;
    foreach ($patterns as $patternKey => $pattern) {
        $rxPost = $rxPre = '';
        if (preg_match('/(-)?([^0#E,\\.\\-+]*)(.+?)([^0#E,\\.\\-+]*)(-)?$/', $pattern, $m)) {
            for ($i = 1; $i < 6; ++$i) {
                if (!isset($m[$i])) {
                    $m[$i] = '';
                }
            }
            if (strlen($m[2]) > 0) {
                $rxPre = preg_quote($m[2]);
            }
            $pattern = $m[1] . $m[3] . $m[5];
            if (strlen($m[4]) > 0) {
                $rxPost = preg_quote($m[4]);
            }
        }
        $rx = '';
        if (strpos($pattern, '.') !== false) {
            list($intPattern, $decimalPattern) = explode('.', $pattern, 2);
        } else {
            $intPattern = $pattern;
            $decimalPattern = '';
        }
        if (strpos($intPattern, 'E') !== false) {
            switch ($intPattern) {
                case '#E0':
                case '#E00':
                    $rx .= '(' . preg_quote($symbols['plusSign']) . ')?[0-9]+((' . preg_quote($symbols['decimal']) . ')[0-9]+)*[eE]((' . preg_quote($symbols['minusSign']) . ')|(' . preg_quote($symbols['plusSign']) . '))?[0-9]+';
                    break;
                case '-#E0':
                case '-#E00':
                    $rx .= '(' . preg_quote($symbols['minusSign']) . ')?[0-9]+((' . preg_quote($symbols['decimal']) . ')[0-9]+)*[eE]((' . preg_quote($symbols['minusSign']) . ')|(' . preg_quote($symbols['plusSign']) . '))?[0-9]+';
                    break;
                default:
                    throw new \Exception("Invalid chunk ('$intPattern') in pattern '$pattern'");
            }
        } elseif (strpos($intPattern, ',') !== false) {
            $chunks = explode(',', $intPattern);
            $maxChunkIndex = count($chunks) - 1;
            $prevChunk = null;
            for ($chunkIndex = 0; $chunkIndex <= $maxChunkIndex; ++$chunkIndex) {
                $chunk = $chunks[$chunkIndex];
                $nextChunk = ($chunkIndex == $maxChunkIndex) ? null : $chunks[$chunkIndex + 1];
                switch ($chunk) {
                    case '#':
                    case '-#':
                        if ($chunk === '-#') {
                            $rx .= '(' . preg_quote($symbols['minusSign']) . ')?';
                        } else {
                            $rx .= '(' . preg_quote($symbols['plusSign']) . ')?';
                        }
                        if ($nextChunk === '##0') {
                            $rx .= '[0-9]{1,3}';
                        } elseif ($nextChunk === '##') {
                            $rx .= '[0-9]{1,2}';
                        } else {
                            throw new \Exception("Invalid chunk #$chunkIndex ('$chunk') in pattern '$pattern'");
                        }
                        break;
                    case '##':
                        if ($nextChunk === '##0') {
                            $rx .= '((' . preg_quote($symbols['group']) . ')?[0-9]{2})*';
                        } else {
                            throw new \Exception("Invalid chunk #$chunkIndex ('$chunk') in pattern '$pattern'");
                        }
                        break;
                    case '##0':
                        if ($prevChunk === '##') {
                            $rx .= '[0-9]';
                        } elseif (($prevChunk === '#') || ($prevChunk === '-#')) {
                            $rx .= '((' . preg_quote($symbols['group']) . ')?[0-9]{3})*';
                        } else {
                            throw new \Exception("Invalid chunk #$chunkIndex ('$chunk') in pattern '$pattern'");
                        }
                        break;
                    case '#0':
                        if ($chunkIndex === 0) {
                            $rx .= '[0-9]*';
                        } else {
                            throw new \Exception("Invalid chunk #$chunkIndex ('$chunk') in pattern '$pattern'");
                        }
                        break;
                }
                $prevChunk = $chunk;
            }
        } else {
            throw new \Exception("Invalid chunk ('$intPattern') in pattern '$pattern'");
        }

        if (strlen($decimalPattern) > 0) {
            switch ($decimalPattern) {
                case '###':
                    $rx .= '((' . preg_quote($symbols['decimal']) . ')[0-9]+)?';
                    break;
                case '###-':
                    $rx .= '((' . preg_quote($symbols['decimal']) . ')[0-9]+)?(' . preg_quote($symbols['minusSign']) . ')';
                    break;
                default:
                    $m = null;
                    if (preg_match('/^(0+)(-?)$/', $decimalPattern, $m)) {
                        $rx .= '(' . preg_quote($symbols['decimal']) . ')[0-9]{' . strlen($m[1]) . '}';
                        if (substr($decimalPattern, -1) === '-') {
                            $rx .= '(' . preg_quote($symbols['minusSign']) . ')';
                        }
                    } else {
                        throw new \Exception("Invalid chunk ('$decimalPattern') in pattern '$pattern'");
                    }
            }
        }

        $result[$patternKey] = '/^' . $rxPre . $rx . $rxPost . '$/u';
    }

    return $result;
}

set_error_handler('handleError');

try {
    echo 'Initializing... ';
    define('CLDR_VERSION', '29-beta-1');
    define('ROOT_DIR', dirname(__DIR__));
    define('SOURCE_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'temp');
    define('DESTINATION_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'data');

    if (isset($argv)) {
        foreach ($argv as $i => $arg) {
            if ($i > 0) {
                if ((strcasecmp($arg, 'debug') === 0) || (strcasecmp($arg, '--debug') === 0)) {
                    defined('DEBUG') or define('DEBUG', true);
                }
                if ((strcasecmp($arg, 'full') === 0) || (strcasecmp($arg, '--full') === 0)) {
                    defined('FULL_JSON') or define('FULL_JSON', true);
                }
                if ((strcasecmp($arg, 'post-clean') === 0) || (strcasecmp($arg, '--post-clean') === 0)) {
                    defined('POST_CLEAN') or define('POST_CLEAN', true);
                }
            }
        }
    }
    defined('DEBUG') or define('DEBUG', false);
    defined('FULL_JSON') or define('FULL_JSON', false);
    define('LOCAL_VCS_DIR', SOURCE_DIR . DIRECTORY_SEPARATOR . 'cldr-' . CLDR_VERSION . '-source');
    defined('POST_CLEAN') or define('POST_CLEAN', false);

    if (!is_dir(SOURCE_DIR)) {
        if (mkdir(SOURCE_DIR, 0777, true) === false) {
            echo 'Failed to create ' . SOURCE_DIR . "\n";
            die(1);
        }
    }
    echo "done.\n";

    if (is_dir(DESTINATION_DIR)) {
        echo 'Cleanup old data folder... ';
        deleteFromFilesystem(DESTINATION_DIR);
        echo "done.\n";
    }
    echo 'Creating data folder... ';
    if (mkdir(DESTINATION_DIR, 0777, false) === false) {
        echo 'Failed to create ' . DESTINATION_DIR . "\n";
        die(1);
    }
    echo "done.\n";

    if (!is_dir(LOCAL_VCS_DIR)) {
        checkoutCLDR();
    }

    buildCLDRJson();

    //copyData();
    if (POST_CLEAN) {
        echo "Cleanup temporary data folder... \n";
        deleteFromFilesystem(SOURCE_DIR);
        echo "done.\n";
    }
    die(0);
} catch (Exception $x) {
    echo $x->getMessage(), "\n";
    die(1);
}
