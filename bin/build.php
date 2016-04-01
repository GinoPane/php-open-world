<?php

/**
 *
 *
 * Script for building initial OpenWorld's data.
 *
 * There's no need to split the script into many files, that's why everything what it needs is placed in the same file.
 * For better understanding, readability and error handling code was split into multiple functions.
 *
 *
 * Initially Based on Punic's build script (https://github.com/punic/punic)
 *
 */

iconv_set_encoding('input_encoding', 'UTF-8');
iconv_set_encoding('internal_encoding', 'UTF-8');
iconv_set_encoding('output_encoding', 'UTF-8');

define('CLDR_VERSION', '29-beta-1');
define('ROOT_DIR', dirname(__DIR__));
define('SOURCE_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'temp');
define('DESTINATION_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'data');
define('DESTINATION_GENERAL_DIR', DESTINATION_DIR . DIRECTORY_SEPARATOR . 'general');
define('DESTINATION_LOCALES_DIR', DESTINATION_DIR . DIRECTORY_SEPARATOR . 'locales');
define('LOCAL_VCS_DIR', SOURCE_DIR . DIRECTORY_SEPARATOR . 'cldr-' . CLDR_VERSION . '-source');

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
defined('POST_CLEAN') or define('POST_CLEAN', false);

/**
 * Enable/Disable supporting output of some functions
 */
$disableOutput = false;

function disableOutput()
{
    global $disableOutput;

    $disableOutput = true;
}

function enableOutput()
{
    global $disableOutput;

    $disableOutput = false;
}

function outputEnabled()
{
    global $disableOutput;

    return $disableOutput == false;
}

/**
 * Worlds most popular languages
 * Sources:
 * https://en.wikipedia.org/wiki/List_of_languages_by_number_of_native_speakers,
 * https://www.loc.gov/standards/iso639-2/php/code_list.php.
 *
 * 1. Mandarin - ZH
 * 2. Spanish - ES
 * 3. English - EN
 * 4. Hindi - HI
 * 5. Arabic - AR
 * 6. Portuguese - PT
 * 7. Bengali - BN
 * 8. Russian - RU
 * 9. Japanese - JA
 * 10. Punjabi - PA
 * 11. German - DE
 * 12. Javanese - JV
 * 13. Wu - WUU
 * 14. Malay - MS
 * 15. Telugu - TE
 * 16. Vietnamese - VI
 * 17. Korean - KO
 * 18. French - FR
 * 19. Marathi - MR
 * 20. Tamil - TA
 * 21. Urdu - UR
 * 22. Turkish - TR
 * 23. Italian - IT
 * 24. Yue - YUE
 * 25. Thai - TH
 * 26. Gujarati - GU
 * 27. Jin - ZH
 * 28. Southern Min - NAN
 * 29. Persian - FA
 * 30. Polish - PL
 *
 */
$defaultLocales = array(
    'zh', 'es', 'en', 'hi', 'ar', 'pt', 'bn', 'ru', 'ja', 'pa', 'de', 'jv', 'wuu', 'ms', 'te',
    'vi', 'ko', 'fr', 'mr', 'ta', 'ur', 'tr', 'it', 'yue', 'th', 'gu', 'zh', 'nan', 'fa', 'pl'
);

/**
 *
 * Service class for XML handling, converts xml to arrays and arrays to xml
 * @see XmlWrapper::arrayToXml, XmlWrapper::xmlToArray
 *
 *
 * @author GinoPane
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
     *
     * Convert an Array to XML
     *
     * @param $nodeName
     * @param array $arr
     * @return DOMElement
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
 * Show a status bar in the console
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
 * @param   string $text additional text to be shown
 * @param   int $size optional size of the status bar
 * @return  void
 *
 * @link    http://stackoverflow.com/questions/2124195/command-line-progress-bar-in-php
 *
 */
function showStatus($done, $total, $text = '', $size = 30)
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

    $statusBar .= $text . "; remaining: " . number_format($eta) . " sec.  elapsed: " . number_format($elapsed) . " sec.";

    echo "$statusBar  ";

    flush();

    // when done, send a newline
    if ($done == $total) {
        echo "\n";
    }
}

/**
 *
 * Custom error handler
 *
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 * @throws Exception
 */
function handleError($errno, $errstr, $errfile, $errline)
{
    if ($errno == E_NOTICE || $errno == E_WARNING) {
        throw new Exception("$errstr in $errfile @ line $errline \n", $errno);
    }
}

/**
 *
 * Handle errors from CLDR checkout
 *
 * @param $directory
 * @param $code
 * @param $output
 * @throws Exception
 */
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

/**
 *
 * Check existence, create directory and handle possible error
 *
 * @param string $directory
 * @return bool
 * @throws Exception
 */
function handleCreateDirectory($directory = "")
{
    if (outputEnabled()) {
        echo "Creating \"$directory\" folder... ";
    }

    if (!is_dir($directory)) {
        if (mkdir($directory, 0777, false) === false) {
            throw new Exception("Failed to create \"$directory\"\n");
        }
    }

    if (outputEnabled()) {
        echo "Done.\n";
    }

    return true;
}

/**
 *
 * Extract currency fractions and region data
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleGeneralCurrencyData($supplementalData = array())
{
    echo "Extract currency fractions data... ";

    if (!isset($supplementalData['supplementalData']['currencyData']['fractions']['info'])) {
        throw new Exception('Currency fractions data is not available!');
    } else {
        $fractions = array();

        foreach($supplementalData['supplementalData']['currencyData']['fractions']['info'] as $key => $fraction) {
            if (!empty($fraction['@attributes'])) {
                $isoCode = $fraction['@attributes']['iso4217'];

                unset($fraction['@attributes']['iso4217']);

                $fractions[$isoCode] = $fraction['@attributes'];
            } else {
                throw new Exception("Wrong fractions data provided (data key: $key)!");
            }
        }

        echo "Done.\n";

        saveJsonFile($fractions, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'currency.fractions.json');
    }

    echo "Extract currency regions data... ";

    if (!isset($supplementalData['supplementalData']['currencyData']['region'])) {
        throw new Exception('Currency regions data is not available!');
    } else {
        $regions = array();

        foreach($supplementalData['supplementalData']['currencyData']['region'] as $key => $region) {
            if (!empty($region['@attributes'])) {
                $isoRegionCode = $region['@attributes']['iso3166'];

                $currencies = array();

                if (isset($region['currency']['@attributes']) && isset($region['currency']['@value'])) {
                    $isoCode = $region['currency']['@attributes']['iso4217'];

                    unset($region['currency']['@attributes']['iso4217']);

                    $currencies[$isoCode] = $region['currency']['@attributes'];
                } else {
                    foreach($region['currency'] as $currencyKey => $currency) {
                        if (!empty($currency['@attributes'])) {
                            $isoCode = $currency['@attributes']['iso4217'];

                            unset($currency['@attributes']['iso4217']);

                            $currencies[$isoCode] = $currency['@attributes'];
                        } else {
                            throw new Exception("Wrong currency data provided for region \"$isoRegionCode\" (data key: $currencyKey)!");
                        }
                    }
                }

                $regions[$isoRegionCode] = $currencies;
            } else {
                throw new Exception("Wrong region data provided (data key: $key)!");
            }
        }

        echo "Done.\n";

        saveJsonFile($regions, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'currency.regions.json');
    }
}

/**
 *
 * Extract territory info data
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleGeneralTerritoryInfoData($supplementalData = array())
{
    echo "Extract territory info data... ";

    if (!isset($supplementalData['supplementalData']['territoryInfo']['territory'])) {
        throw new Exception('Territory info data is not available!');
    } else {
        $territories = array();

        foreach ($supplementalData['supplementalData']['territoryInfo']['territory'] as $key => $territory) {
            if (!empty($territory['@attributes'])) {
                $isoRegionCode = $territory['@attributes']['type'];

                unset($territory['@attributes']['type']);

                $languageData = array();

                if (isset($territory['languagePopulation'])) {

                    if (isset($territory['languagePopulation']['@attributes']) && isset($territory['languagePopulation']['@value'])) {
                        $languageCode = $territory['languagePopulation']['@attributes']['type'];

                        unset($territory['languagePopulation']['@attributes']['type']);

                        $languageData[$languageCode] = $territory['languagePopulation']['@attributes'];
                    } else {
                        foreach ($territory['languagePopulation'] as $languageKey => $language) {
                            if (!empty($language['@attributes'])) {
                                $languageCode = $language['@attributes']['type'];

                                unset($language['@attributes']['type']);

                                $languageData[$languageCode] = $language['@attributes'];
                            } else {
                                throw new Exception("Wrong language data provided for region \"$isoRegionCode\" (data key: $languageKey)!");
                            }
                        }
                    }
                }

                unset($territory['languagePopulation']);

                if ($languageData) {
                    $territory['@attributes']['languageData'] = $languageData;
                }

                $territories[$isoRegionCode] = $territory['@attributes'];
            } else {
                throw new Exception("Wrong territory data provided (data key: $key)!");
            }
        }

        echo "Done.\n";

        saveJsonFile($territories, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'territory.info.json');
    }
}

/**
 *
 * Extract territory containment info and flat info for quick search
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleGeneralTerritoryContainmentData($supplementalData = array())
{
    echo "Extract territory containment data... ";

    if (!isset($supplementalData['supplementalData']['territoryContainment']['group'])) {
        throw new Exception('Territory containment data is not available!');
    } else {
        $containment = array();

        foreach ($supplementalData['supplementalData']['territoryContainment']['group'] as $key => $territory) {
            if (!empty($territory['@attributes'])) {
                $territoryCode = $territory['@attributes']['type'];

                unset($territory['@attributes']['type']);

                $languageData = array();

                if ($languageData) {
                    $territory['@attributes']['languageData'] = $languageData;
                }

                $containment[$territoryCode] = $territory['@attributes'];
            } else {
                throw new Exception("Wrong territory containment data provided (data key: $key)!");
            }
        }

        $flatTerritoryParentData = array();

        foreach ($containment as $parentTerritoryId => $data) {
            if (!empty($data['contains'])) {
                $children = explode(' ', $data['contains']);

                foreach($children as $childCode) {
                    $flatTerritoryParentData[$childCode] = (string)$parentTerritoryId;
                }
            }
        }

        ksort($flatTerritoryParentData);

        $territories = array(
            'containment'   => $containment,
            'flat'          => $flatTerritoryParentData
        );

        saveJsonFile($territories, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'territory.containment.json', JSON_FORCE_OBJECT);

        echo "Done.\n";
    }
}


/**
 *
 * Extract territory codes mapping data
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleGeneralTerritoryMapping($supplementalData = array())
{
    echo "Extract territory codes mapping... ";

    if (!isset($supplementalData['supplementalData']['codeMappings']['territoryCodes'])) {
        throw new Exception('Bad territory codes mapping data!');
    } else {
        $iso3166Alpha2 = array();
        $iso3166Alpha3Map = array();
        $iso3166NumericMap = array();
        $fips10Map = array();

        foreach($supplementalData['supplementalData']['codeMappings']['territoryCodes'] as $codesMap) {
            $codes = $codesMap['@attributes'];

            $iso3166Alpha2[] = $codes['type'];

            if (isset($codes['alpha3'])) {
                $iso3166Alpha3Map[$codes['alpha3']] = $codes['type'];
            }

            if (isset($codes['numeric'])) {
                $iso3166NumericMap[(string)$codes['numeric']] = $codes['type'];
            }

            if (isset($codes['fips10'])) {
                $fips10Map[$codes['fips10']] = $codes['type'];
            }
        }

        $territoryCodes = array(
            'iso3166alpha2' => $iso3166Alpha2,
            'iso3166alpha3_to_iso3166alpha2' => $iso3166Alpha3Map,
            'iso3166numeric_to_iso3166alpha2' => $iso3166NumericMap,
            'fips10_to_iso3166alpha2' => $fips10Map
        );

        saveJsonFile($territoryCodes, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'territory.codes.json');

        echo "Done.\n";
    }
}

/**
 *
 * Extract territory codes mapping data
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleGeneralCurrencyMapping($supplementalData = array())
{
    echo "Extract currency codes mapping... ";

    if (!isset($supplementalData['supplementalData']['codeMappings']['currencyCodes'])) {
        throw new Exception('Bad currency codes mapping data!');
    } else {
        $iso4217Alpha = array();
        $iso4217Numeric = array();

        foreach($supplementalData['supplementalData']['codeMappings']['currencyCodes'] as $codesMap) {
            $codes = $codesMap['@attributes'];

            $iso4217Alpha[] = $codes['type'];

            if (isset($codes['numeric'])) {
                $iso4217Numeric[sprintf('%03d', $codes['numeric'])] = $codes['type'];
            }
        }

        $currencyCodes = array(
            'iso4217alpha' => $iso4217Alpha,
            'iso4217numeric_to_iso4217alpha' => $iso4217Numeric
        );

        saveJsonFile($currencyCodes, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'currency.codes.json');

        echo "Done.\n";
    }
}

/**
 *
 * Extract numbering systems data
 *
 * @param array $numbersData
 * @throws Exception
 */
function handleNumberingSystemsData($numbersData = array())
{
    echo "Extract numbering systems data... ";

    if (!isset($numbersData['supplementalData']['numberingSystems']['numberingSystem'])) {
        throw new Exception('Numbering systems data is not available!');
    } else {
        $numberingSystems = array();

        foreach ($numbersData['supplementalData']['numberingSystems']['numberingSystem'] as $key => $system) {
            if (!empty($system['@attributes'])) {
                $systemCode = $system['@attributes']['id'];

                unset($system['@attributes']['type']);

                if (isset($system['@attributes']['digits'])) {
                    $system['@attributes']['digits'] = preg_split('/(?<!^)(?!$)/u', $system['@attributes']['digits']);
                }

                $numberingSystems[$systemCode] = $system;
            } else {
                throw new Exception("Wrong numbering system data provided (data key: $key)!");
            }
        }

        saveJsonFile($numberingSystems, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'number.systems.json', JSON_FORCE_OBJECT);

        echo "Done.\n";
    }
}

/**
 *
 * Extract identity data for single locale
 *
 * @param array $identityData
 * @param string $destinationDir
 * @throws Exception
 */
function handleSingleLocaleDataIdentity($identityData = array(), $destinationDir = "")
{
    if (!isset($identityData['version']) || !isset($identityData['language'])) {
        throw new Exception('Bad identity data detected!');
    } else {
        $version = filter_var($identityData['version']['@attributes']['number'], FILTER_SANITIZE_NUMBER_INT);
        $language = $identityData['language']['@attributes']['type'];

        $identity = array(
            'version'   => $version,
            'language'  => $language
        );

        if (isset($identityData['territory'])) {
            $identity['territory'] = $identityData['territory']['@attributes']['type'];
        }

        saveJsonFile($identity, $destinationDir . DIRECTORY_SEPARATOR . 'identity.json', JSON_FORCE_OBJECT);
    }
}

/**
 *
 * Extract naming data for such simple data lists as territory, language, script names
 *
 * @param $type
 * @param $rawData
 * @param $destinationDir
 * @param $fileName
 * @throws Exception
 */
function handleSingleLocaleDataSimpleNames($type, $rawData, $destinationDir, $fileName)
{
    $data = array();

    foreach($rawData as $rawRowData) {
        if (!isset($rawRowData['@value']) || !isset($rawRowData['@attributes']['type'])) {
            throw new Exception("Bad $type data detected!");
        } else {
            $code = $rawRowData['@attributes']['type'];
            $name = $rawRowData['@value'];

            $data[$code] = $name;
        }
    }

    saveJsonFile($data, $destinationDir . DIRECTORY_SEPARATOR . $fileName, JSON_FORCE_OBJECT);
}

/**
 * 
 * Build different kinds of data for a single locale
 * 
 * @param $locale
 * @param $localeFile
 * @throws Exception
 */
function handleSingleLocaleData($locale, $localeFile)
{
    $localeData = getXmlDataFileContentsAsArray($localeFile);

    if ($localeData) {
        if (!isset($localeData['ldml']['identity'])) {
            throw new Exception("Failed to identify \"$locale\" locale data");
        }

        $localeDirectory = DESTINATION_LOCALES_DIR . DIRECTORY_SEPARATOR . $locale;

        if (handleCreateDirectory($localeDirectory)) {
            //handleSingleLocaleDataIdentity($localeData['ldml']['identity'], $localeDirectory);

            if (isset($localeData['ldml']['localeDisplayNames']['territories']['territory'])) {
                handleSingleLocaleDataSimpleNames('territory', $localeData['ldml']['localeDisplayNames']['territories']['territory'], $localeDirectory, 'territory.names.json');
            }

            if (isset($localeData['ldml']['localeDisplayNames']['languages']['language'])) {
                handleSingleLocaleDataSimpleNames('language', $localeData['ldml']['localeDisplayNames']['languages']['language'], $localeDirectory, 'language.names.json');
            }

            if (isset($localeData['ldml']['localeDisplayNames']['scripts']['script'])) {
                handleSingleLocaleDataSimpleNames('script', $localeData['ldml']['localeDisplayNames']['scripts']['script'], $localeDirectory, 'script.names.json');
            }

            //handleSingleLocaleDataNumbers();
            //handleSingleLocaleDataTerritoryNames();
            //handleSingleLocaleDataCurrencies();
        }

    } else {
        throw new Exception("Failed to get \"$locale\" locale data");
    }
}

/**
 * @param $fileName
 * @return array
 * @throws Exception
 */
function getXmlDataFileContentsAsArray($fileName)
{
    if (outputEnabled()) {
        echo "Checking \"$fileName\"...\n";
    }

    if (!is_readable($fileName)) {
        throw new Exception("$fileName is not found or is not readable! \n");
    }

    if (outputEnabled()) {
        echo "File is available. Processing...\n";
    }

    return XmlWrapper::getParser()->xmlToArray($fileName);
}

/**
 * @param $fileName
 * @param array $handlers
 * @throws Exception
 */
function processDataFileWithHandlers($fileName, $handlers = array())
{
    if ($fileName) {
        if ($data = getXmlDataFileContentsAsArray($fileName)) {
            foreach($handlers as $handler) {
                if (is_callable($handler)) {
                    call_user_func($handler, $data);
                } else {
                    throw new Exception("Bad handler passed: $handler");
                }
            }
        } else {
            throw new Exception("Failed to get data from datafile: $fileName");
        }
    }
}

/**
 *
 * SVN checkout CLDR data
 *
 * @throws Exception
 */
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

        echo "Done.\n";
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
    echo "Determining the list of the available locales... ";

    $availableLocales = array();
    $localesDirectory = LOCAL_VCS_DIR . DIRECTORY_SEPARATOR . 'main';

    $contents = @scandir($localesDirectory);

    if ($contents === false) {
        throw new Exception("Error reading contents of the directory \"$localesDirectory\"");
    }

    $match = null;

    foreach ($contents as $item) {
        if (preg_match('/^(.+)\.xml$/', $item, $match)) {
            $availableLocales[] = str_replace('_', '-', $match[1]);
        }
    }

    if (empty($availableLocales)) {
        throw new Exception("No locales found!");
    }

    if (FULL_JSON) {
        $locales = $availableLocales;
    } else {
        echo "Checking default locales based on statistics of most popular languages... \n";

        global $defaultLocales;

        $locales = array_merge($defaultLocales, array('root'));
        $available = array_intersect($locales, $availableLocales);

        if (!count($available) !== count($locales)) {
             echo "Notice: the following locales were not found:\n- " . implode("\n- ", array_diff($locales, $availableLocales)) . "\n";
        }

        $locales = array_unique($available);
    }

    sort($locales);

    $overallCount = count($locales);

    echo $overallCount . " locales available (including root).\n";

    disableOutput();

    foreach ($locales as $key => $locale) {
        handleSingleLocaleData($locale, $localesDirectory . DIRECTORY_SEPARATOR . $locale . ".xml");

        showStatus($key + 1, $overallCount, " Processed \"$locale\"", 50);
    }

    enableOutput();

    echo "Done.\n";
}

/*
 * Build supplemental data for CLDR
 */
function buildSupplementalData()
{
    $supplementalDataFile = LOCAL_VCS_DIR . str_replace("/", DIRECTORY_SEPARATOR, "/supplemental/supplementalData.xml");
    $numberingSystemsDataFile = LOCAL_VCS_DIR . str_replace("/", DIRECTORY_SEPARATOR, "/supplemental/numberingSystems.xml");

    $dataHandlers = array(
        'supplemental'  => array(
            $supplementalDataFile => array(
                //'handleGeneralCurrencyData',
                //'handleGeneralTerritoryInfoData',
                //'handleGeneralTerritoryContainmentData',
                //'handleGeneralTerritoryMapping',
                'handleGeneralCurrencyMapping'
            )
        ),
        'numeric'       => array(
            $numberingSystemsDataFile => array(
                //'handleNumberingSystemsData'
            )
        )
    );

    foreach($dataHandlers as $dataCategory => $handlersPerFile) {
        if ($handlersPerFile) {
            echo "Building $dataCategory data... \n";

            foreach ($handlersPerFile as $fileName => $handlers) {
                processDataFileWithHandlers($fileName, $handlers);
            }

            echo "$dataCategory data was built. \n";
        }
    }
    die();
}

function buildCLDRJson()
{
    buildSupplementalData();

    buildLocaleSpecificData();
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
        throw new Exception("Failed to read from \"$file\"");
    }

    $data = json_decode($json, true);

    if ($data === null) {
        throw new Exception("Failed to decode data in \"$file\"");
    }

    return $data;
}

function saveJsonFile($data, $file, $jsonFlags = 0)
{
    if (outputEnabled()) {
        echo "Saving data to \"$file\"... ";
    }

    if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
        $jsonFlags |= JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        if (DEBUG) {
            $jsonFlags |= JSON_PRETTY_PRINT;
        }
    }
    $json = json_encode($data, $jsonFlags);
    if ($json === false) {
        throw new Exception("Failed to serialize data for \"$file\"");
    }
    if (is_file($file)) {
        deleteFromFilesystem($file);
    }
    if (file_put_contents($file, $json) === false) {
        throw new Exception("Failed write to \"$file\"");
    }

    if (outputEnabled()) {
        echo "Done \n";
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
    echo "Initializing...\n";

    handleCreateDirectory(SOURCE_DIR);

    if (is_dir(DESTINATION_DIR)) {
        echo "Cleanup old data folder... ";
        deleteFromFilesystem(DESTINATION_DIR);
        echo "Done.\n";
    }

    handleCreateDirectory(DESTINATION_DIR);
    handleCreateDirectory(DESTINATION_GENERAL_DIR);
    handleCreateDirectory(DESTINATION_LOCALES_DIR);

    if (!is_dir(LOCAL_VCS_DIR)) {
        checkoutCLDR();
    }

    buildCLDRJson();

    if (POST_CLEAN) {
        echo "Cleanup temporary data folder... \n";
        deleteFromFilesystem(SOURCE_DIR);
        echo "Done.\n";
    }
    die(0);
} catch (Exception $x) {
    deleteFromFilesystem(DESTINATION_DIR);

    echo $x->getMessage(), "\n";

    if (POST_CLEAN) {
        echo "Cleanup generated data folder... ";
        deleteFromFilesystem(SOURCE_DIR);
        echo "Done.\n";
    } else {
        echo "Some data files probably were not generated. Check \"" . DESTINATION_DIR . "\" folder.\n";
    }

    die(1);
}
