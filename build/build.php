<?php

/**
 * Script for building initial OpenWorld's data.
 *
 * There's no need to split the script into many files, that's why everything what it needs is placed in the same file.
 * For better understanding, readability and error handling code was split into multiple functions.
 *
 *
 * Initially Based on Punic's build script (https://github.com/punic/punic)
 *
 * Data is provided by CLDR (Unicode Common Locale Data Repository) - http://cldr.unicode.org/
 * Information about data files markup can be found at LDML documentation (Locale Data Markup Language) - http://unicode.org/reports/tr35/
 *
 * @author Sergey <Gino Pane> Karavay
 *
 */

iconv_set_encoding('input_encoding', 'UTF-8');
iconv_set_encoding('internal_encoding', 'UTF-8');
iconv_set_encoding('output_encoding', 'UTF-8');

define('CLDR_VERSION', '29');
define('ROOT_DIR', dirname(__DIR__));
define('SOURCE_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'temp');
define('DESTINATION_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'data');
define('DESTINATION_GENERAL_DIR', DESTINATION_DIR . DIRECTORY_SEPARATOR . 'general');
define('DESTINATION_LOCALES_DIR', DESTINATION_DIR . DIRECTORY_SEPARATOR . 'locales');
define('LOCAL_VCS_DIR', SOURCE_DIR . DIRECTORY_SEPARATOR . 'cldr-' . CLDR_VERSION . '-source');

if (isset($argv)) {
    foreach ($argv as $i => $arg) {
        if ($i > 0) {
            if ((strcasecmp($arg, 'debug') === 0) || (strcasecmp($arg, '--debug') === 0)) {
                define('DEBUG', true);
            }
            if ((strcasecmp($arg, 'full') === 0) || (strcasecmp($arg, '--full') === 0)) {
                define('FULL_JSON', true);
            }
            if ((strcasecmp($arg, 'post-clean') === 0) || (strcasecmp($arg, '--post-clean') === 0)) {
                define('POST_CLEAN', true);
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

/**
 * Set flag of disabled output
 */
function disableOutput()
{
    global $disableOutput;

    $disableOutput = true;
}

/**
 * Set flag of enabled output
 */
function enableOutput()
{
    global $disableOutput;

    $disableOutput = false;
}

/**
 * Check output flag
 *
 * @return bool
 */
function outputEnabled()
{
    global $disableOutput;

    return $disableOutput == false;
}

/**
 * World's most popular languages
 *
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
define('DEFAULT_LOCALES',
    [
        'zh', 'es', 'en', 'hi', 'ar', 'pt', 'bn', 'ru', 'ja', 'pa', 'de', 'jv', 'wuu', 'ms', 'te',
        'vi', 'ko', 'fr', 'mr', 'ta', 'ur', 'tr', 'it', 'yue', 'th', 'gu', 'zh', 'nan', 'fa', 'pl'
    ]
);

/**
 * Service class for XML handling, converts xml to arrays and arrays to xml
 * @see XmlWrapper::arrayToXml, XmlWrapper::xmlToArray
 *
 *
 * @author Sergey <Gino Pane> Karavay
 *
 */
class XmlWrapper
{
    private $_xml = null;
    private $_encoding = 'UTF-8';
    private $_options = [];

    private function __construct($options = [])
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
    public static function getParser($options = [])
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
    public function arrayToXml(array $data, $options = [])
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
            $output = [];

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
    private function _convertArrayToXml($nodeName, $arr = [])
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
                    $numericKeyName = $this->_options['numericKeysName'] ?? $key;
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
        $output = [];

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
                            $output[$t] = [];
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
                        $output = ['@value' => $output];
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
        $matches = [];
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

    $statusBar .= $text . "; " . number_format($eta) . " / " . number_format($elapsed) . " sec";

    echo "$statusBar  ";

    flush();

    // when done, send a newline
    if ($done == $total) {
        echo "\n";
    }
}

/**
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
 * Check existence, create directory and handle any possible error
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
 * Extract currency fractions and region data
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleGeneralCurrencyData($supplementalData = [])
{
    echo "Extract currency fractions data... ";

    if (!isset($supplementalData['supplementalData']['currencyData']['fractions']['info'])) {
        throw new Exception('Currency fractions data is not available!');
    } else {
        $fractions = [];

        foreach ($supplementalData['supplementalData']['currencyData']['fractions']['info'] as $key => $fraction) {
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

        foreach ($supplementalData['supplementalData']['currencyData']['region'] as $key => $region) {
            if (!empty($region['@attributes'])) {
                $isoRegionCode = $region['@attributes']['iso3166'];

                $currencies = [];

                if (isset($region['currency']['@attributes']) && isset($region['currency']['@value'])) {
                    $isoCode = $region['currency']['@attributes']['iso4217'];

                    unset($region['currency']['@attributes']['iso4217']);

                    $currencies[$isoCode] = $region['currency']['@attributes'];
                } else {
                    foreach ($region['currency'] as $currencyKey => $currency) {
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

        saveJsonFile($regions, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'territory.currencies.json');
    }
}

/**
 * Extract territory info data
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleGeneralTerritoryInfoData($supplementalData = [])
{
    echo "Extract territory info data... ";

    if (!isset($supplementalData['supplementalData']['territoryInfo']['territory'])) {
        throw new Exception('Territory info data is not available!');
    } else {
        $territories = [];
        $languageInfo = [];

        foreach ($supplementalData['supplementalData']['territoryInfo']['territory'] as $key => $territory) {
            if (!empty($territory['@attributes'])) {
                $isoRegionCode = $territory['@attributes']['type'];

                unset($territory['@attributes']['type']);

                $languageData = [];

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

                    $languages = array_keys($languageData);

                    foreach ($languages as $language) {
                        if (!isset($languageInfo[$language])) {
                            $languageInfo[$language] = [];
                        }

                        $languageInfo[$language][] = $isoRegionCode;
                    }
                }

                $territories[$isoRegionCode] = $territory['@attributes'];
            } else {
                throw new Exception("Wrong territory data provided (data key: $key)!");
            }
        }

        echo "Done.\n";

        ksort($languageInfo);

        saveJsonFile($territories, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'territory.info.json');
        saveJsonFile($languageInfo, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'language.territories.json');
    }
}

/**
 * Extract territory containment info and flat info for quick search
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleGeneralTerritoryContainmentData($supplementalData = [])
{
    echo "Extract territory containment data... ";

    if (!isset($supplementalData['supplementalData']['territoryContainment']['group'])) {
        throw new Exception('Territory containment data is not available!');
    } else {
        $containment = [];

        foreach ($supplementalData['supplementalData']['territoryContainment']['group'] as $key => $territory) {
            if (!empty($territory['@attributes'])) {
                $territoryCode = $territory['@attributes']['type'];

                unset($territory['@attributes']['type']);

                $containment[$territoryCode] = $territory['@attributes'];
            } else {
                throw new Exception("Wrong territory containment data provided (data key: $key)!");
            }
        }

        $flatTerritoryParentData = [];

        foreach ($containment as $parentTerritoryId => $data) {
            if (!empty($data['contains'])) {
                $children = explode(' ', $data['contains']);

                foreach ($children as $childCode) {
                    $flatTerritoryParentData[$childCode] = (string)$parentTerritoryId;
                }
            }
        }

        ksort($flatTerritoryParentData);

        $territories = [
            'containment' => $containment,
            'flat' => $flatTerritoryParentData
        ];

        saveJsonFile($territories, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'territory.containment.json', JSON_FORCE_OBJECT);

        echo "Done.\n";
    }
}

/**
 * Extract territory codes mapping data
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleGeneralTerritoryMapping($supplementalData = [])
{
    echo "Extract territory codes mapping... ";

    if (!isset($supplementalData['supplementalData']['codeMappings']['territoryCodes'])) {
        throw new Exception('Bad territory codes mapping data!');
    } else {
        $iso3166Alpha2 = [];
        $iso3166Alpha3Map = [];
        $iso3166NumericMap = [];
        $fips10Map = [];

        foreach ($supplementalData['supplementalData']['codeMappings']['territoryCodes'] as $codesMap) {
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

        $territoryCodes = [
            'iso3166alpha2' => $iso3166Alpha2,
            'iso3166alpha3_to_iso3166alpha2' => $iso3166Alpha3Map,
            'iso3166numeric_to_iso3166alpha2' => $iso3166NumericMap,
            'fips10_to_iso3166alpha2' => $fips10Map
        ];

        saveJsonFile($territoryCodes, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'territory.codes.json');

        echo "Done.\n";
    }
}

/**
 * Extract territory codes mapping data
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleGeneralCurrencyMapping($supplementalData = [])
{
    echo "Extract currency codes mapping... ";

    if (!isset($supplementalData['supplementalData']['codeMappings']['currencyCodes'])) {
        throw new Exception('Bad currency codes mapping data!');
    } else {
        $iso4217Alpha = [];
        $iso4217Numeric = [];

        foreach ($supplementalData['supplementalData']['codeMappings']['currencyCodes'] as $codesMap) {
            $codes = $codesMap['@attributes'];

            $iso4217Alpha[] = $codes['type'];

            if (isset($codes['numeric'])) {
                $iso4217Numeric[sprintf('%03d', $codes['numeric'])] = $codes['type'];
            }
        }

        $currencyCodes = [
            'iso4217alpha' => $iso4217Alpha,
            'iso4217numeric_to_iso4217alpha' => $iso4217Numeric
        ];

        saveJsonFile($currencyCodes, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'currency.codes.json');

        echo "Done.\n";
    }
}

/**
 * Extract parent locales mapping data
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleGeneralParentLocales($supplementalData = [])
{
    echo "Extract parent locales mapping... ";

    if (!isset($supplementalData['supplementalData']['parentLocales']['parentLocale'])) {
        throw new Exception('Bad parent locales mapping data!');
    } else {
        $localeParents = [];

        foreach ($supplementalData['supplementalData']['parentLocales']['parentLocale'] as $parentLocaleMapping) {
            $parent = $parentLocaleMapping['@attributes']['parent'];
            $locales = $parentLocaleMapping['@attributes']['locales'];

            $localeParents = array_merge($localeParents, array_fill_keys(explode(" ", $locales), $parent));
        }

        saveJsonFile($localeParents, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'locale.parents.json');

        echo "Done.\n";
    }
}

/**
 * Extract language alias data
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleLanguageAlias($supplementalData = [])
{
    echo "Extract language alias mapping... ";

    if (!isset($supplementalData['supplementalData']['metadata']['alias']['languageAlias'])) {
        throw new Exception('Bad language alias data!');
    } else {
        $languageAlias = [];

        foreach ($supplementalData['supplementalData']['metadata']['alias']['languageAlias'] as $alias) {
            $languageAlias[$alias['@attributes']['type']] = [
                'replacement' => $alias['@attributes']['replacement'],
                'reason' => $alias['@attributes']['reason']
            ];
        }

        saveJsonFile($languageAlias, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'language.alias.json');

        echo "Done.\n";
    }
}

/**
 * Extract territory alias data
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleTerritoryAlias($supplementalData = [])
{
    echo "Extract territory alias mapping... ";

    if (!isset($supplementalData['supplementalData']['metadata']['alias']['territoryAlias'])) {
        throw new Exception('Bad territory alias data!');
    } else {
        $territoryAlias = [];

        foreach ($supplementalData['supplementalData']['metadata']['alias']['territoryAlias'] as $alias) {
            $territoryAlias[$alias['@attributes']['type']] = [
                'replacement' => $alias['@attributes']['replacement'],
                'reason' => $alias['@attributes']['reason']
            ];
        }

        saveJsonFile($territoryAlias, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'territory.alias.json');

        echo "Done.\n";
    }
}

/**
 * Extract likely subtags data
 *
 * @param array $supplementalData
 * @throws Exception
 */
function handleLikelySubtagsData($supplementalData = [])
{
    echo "Extract likely subtags data mapping... ";

    if (!isset($supplementalData['supplementalData']['likelySubtags']['likelySubtag'])) {
        throw new Exception('Bad likely subtags data!');
    } else {
        $likelySubtags = [];

        foreach ($supplementalData['supplementalData']['likelySubtags']['likelySubtag'] as $data) {
            list($locale, $script, $territory) = explode('_', $data['@attributes']['to']);

            $likelySubtags[$data['@attributes']['from']] = [
                'locale' => $locale,
                'script' => $script,
                'territory' => $territory
            ];
        }

        saveJsonFile($likelySubtags, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'likely.subtags.json');

        echo "Done.\n";
    }
}

/**
 * Extract numbering systems data
 *
 * @param array $numbersData
 * @throws Exception
 */
function handleNumberingSystemsData($numbersData = [])
{
    echo "Extract numbering systems data... ";

    if (!isset($numbersData['supplementalData']['numberingSystems']['numberingSystem'])) {
        throw new Exception('Numbering systems data is not available!');
    } else {
        $numberingSystems = [];

        foreach ($numbersData['supplementalData']['numberingSystems']['numberingSystem'] as $key => $system) {
            if (!empty($system['@attributes'])) {
                $systemCode = $system['@attributes']['id'];

                unset($system['@attributes']['id']);

                if (isset($system['@attributes']['digits'])) {
                    $system['@attributes']['digits'] = preg_split('/(?<!^)(?!$)/u', $system['@attributes']['digits']);
                }

                $numberingSystems[$systemCode] = $system['@attributes'];
            } else {
                throw new Exception("Wrong numbering system data provided (data key: $key)!");
            }
        }

        saveJsonFile($numberingSystems, DESTINATION_GENERAL_DIR . DIRECTORY_SEPARATOR . 'number.systems.json', JSON_FORCE_OBJECT);

        echo "Done.\n";
    }
}

/**
 * Extract identity data for a single locale
 *
 * @param array $identityData
 * @param string $destinationDir
 * @throws Exception
 */
function handleSingleLocaleDataIdentity($identityData = [], $destinationDir = "")
{
    if (!isset($identityData['version']) || !isset($identityData['language'])) {
        throw new Exception('Bad identity data detected!');
    } else {
        $version = filter_var($identityData['version']['@attributes']['number'], FILTER_SANITIZE_NUMBER_INT);
        $language = $identityData['language']['@attributes']['type'];

        $identity = [
            'version' => $version,
            'language' => $language
        ];

        if (isset($identityData['territory'])) {
            $identity['territory'] = $identityData['territory']['@attributes']['type'];
        }

        saveJsonFile($identity, $destinationDir . DIRECTORY_SEPARATOR . 'identity.json', JSON_FORCE_OBJECT);
    }
}

/**
 * Extract currency data for a single locale
 *
 * @param array $currenciesData
 * @param string $destinationDir
 * @throws Exception
 */
function handleSingleLocaleDataCurrencies($currenciesData = [], $destinationDir = "")
{
    $currencies = [];

    $handleSingleCurrency = function ($rawCurrencyData, &$currencies) {
        if (!isset($rawCurrencyData['@attributes']['type'])) {
            throw new Exception('Bad data for currency!');
        }

        $currencyData = [];
        $currencyData['names'] = [];

        if (isset($rawCurrencyData['displayName'])) {
            if (is_array($rawCurrencyData['displayName'])) {
                if (!isset($rawCurrencyData['displayName']['@value'])) {
                    foreach ($rawCurrencyData['displayName'] as $nameData) {
                        if (is_array($nameData)) {
                            if (isset($nameData['@attributes']['count'])) {
                                $currencyData['names'][$nameData['@attributes']['count']] = $nameData['@value'];
                            } else {
                                $currencyData['names']['default'] = $nameData['@value'];
                            }
                        } else {
                            $currencyData['names']['default'] = $nameData;
                        }
                    }
                } else {
                    $currencyData['names']['default'] = $rawCurrencyData['displayName']['@value'];
                }
            } else {
                $currencyData['names']['default'] = $rawCurrencyData['displayName'];
            }
        } else {
            $currencyData['names']['default'] = '';
        }

        $currencyData['symbols'] = [];

        if (isset($rawCurrencyData['symbol'])) {
            if (is_array($rawCurrencyData['symbol'])) {
                if (!isset($rawCurrencyData['symbol']['@value'])) {
                    foreach ($rawCurrencyData['symbol'] as $symbolData) {
                        if (is_array($symbolData)) {
                            if (isset($symbolData['@attributes']['alt'])) {
                                $currencyData['symbols'][$symbolData['@attributes']['alt']] = $symbolData['@value'];
                            } else {
                                $currencyData['symbols']['default'] = $symbolData['@value'];
                            }
                        } else {
                            $currencyData['symbols']['default'] = $symbolData;
                        }
                    }
                } else {
                    $currencyData['symbols']['default'] = $rawCurrencyData['symbol']['@value'];
                }
            } else {
                $currencyData['symbols']['default'] = $rawCurrencyData['symbol'];
            }
        } else {
            $currencyData['symbols']['default'] = '';
        }

        $currencies[$rawCurrencyData['@attributes']['type']] = $currencyData;
    };

    if (!isset($currenciesData['@attributes']['type'])) {
        foreach ($currenciesData as $rawCurrencyData) {
            $handleSingleCurrency($rawCurrencyData, $currencies);
        }
    } else {
        $handleSingleCurrency($currenciesData, $currencies);
    }

    saveJsonFile($currencies, $destinationDir . DIRECTORY_SEPARATOR . 'currency.names.json', JSON_FORCE_OBJECT);
}

/**
 * Extract symbols data for a single locale
 *
 * @param array $symbolsData
 * @param string $destinationDir
 * @throws Exception
 */
function handleSingleLocaleDataSymbols($symbolsData = [], $destinationDir = "")
{
    $handleSingleNumberingSystem = function ($symbolsData, &$symbols) {
        if (isset($symbolsData['@attributes']['numberSystem'])) {
            $numberingSystem = $symbolsData['@attributes']['numberSystem'];
            $data = [];

            unset($symbolsData['@attributes']);

            foreach ($symbolsData as $name => $value) {
                if (!is_array($value)) {
                    $data[$name] = $value;
                } else {
                    if ($name !== 'alias') {
                        if (isset($value['@value'])) {
                            $data[$name] = $value['@value'];
                        } else {
                            foreach ($value as $subValue) {
                                if (is_string($subValue)) {
                                    $data[$name]['default'] = $subValue;
                                } elseif (isset($subValue['@value']) && isset($subValue['@attributes']['alt'])) {
                                    $data[$name][$subValue['@attributes']['alt']] = $subValue['@value'];
                                }
                            }
                        }
                    } else {
                        $matches = [];

                        preg_match("/'(.+)'/", $value['@attributes']['path'], $matches);

                        if ($matches[1]) {
                            $data[$name] = $matches[1];
                        }
                    }
                }
            }

            $symbols[$numberingSystem] = $data;
        }
    };

    $symbols = [];

    if (isset($symbolsData['@attributes'])) {
        $handleSingleNumberingSystem($symbolsData, $symbols);
    } else {
        foreach ($symbolsData as $numberingSystemSymbolData) {
            $handleSingleNumberingSystem($numberingSystemSymbolData, $symbols);
        }
    }

    saveJsonFile($symbols, $destinationDir . DIRECTORY_SEPARATOR . 'number.symbols.json', JSON_FORCE_OBJECT);
}

/**
 * Extract symbols data for a single locale
 *
 * @param array $formatsData
 * @param string $destinationDir
 * @throws Exception
 */
function handleSingleLocaleDataCurrencyFormats($formatsData = [], $destinationDir = "")
{
    $handleSingleNumberingSystemCurrencyFormat = function ($formatData, &$currencyFormats) {
        $handleSingleCurrencyFormatLength = function ($currencyFormatLength, &$numberingSystemCurrencyFormats) {
            $handleSinglePatternType = function ($patternType, &$currencyFormatType) {
                if (isset($patternType['alias'])) {
                    $matches = [];

                    preg_match("/'(.+)'/", $patternType['alias']['@attributes']['path'], $matches);

                    if ($matches[1]) {
                        $currencyFormatType[$patternType['@attributes']['type']] = ['alias' => $matches[1]];
                    }
                } else {
                    if (is_array($patternType['pattern']) && !isset($patternType['pattern']['@attributes'])) {
                        $patterns = [];

                        foreach ($patternType['pattern'] as $patternData) {
                            $patterns[$patternData['@attributes']['type']][$patternData['@attributes']['count']] = $patternData['@value'];
                        }

                        $currencyFormatType[$patternType['@attributes']['type']] = $patterns;
                    } else {
                        $currencyFormatType[(isset($patternType['pattern']['@attributes']['type']) ? $patternType['pattern']['@attributes']['type'] : 'default')] =
                            isset($patternType['pattern']['@value']) ? $patternType['pattern']['@value'] : (string)$patternType['pattern'];
                    }
                }
            };

            $currencyFormatType = 'default';

            if (isset($currencyFormatLength['@attributes']['type'])) {
                $currencyFormatType = $currencyFormatLength['@attributes']['type'];
            }

            if (isset($currencyFormatLength['currencyFormat']['pattern'])) {
                $handleSinglePatternType($currencyFormatLength['currencyFormat'], $numberingSystemCurrencyFormats[$currencyFormatType]);
            } else {
                foreach ($currencyFormatLength['currencyFormat'] as $patternType) {
                    $handleSinglePatternType($patternType, $numberingSystemCurrencyFormats[$currencyFormatType]);
                }
            }
        };

        if (isset($formatData['alias']) && isset($formatData['@attributes']['numberSystem'])) {
            $matches = [];

            preg_match("/'(.+)'/", $formatData['alias']['@attributes']['path'], $matches);

            if ($matches[1]) {
                $currencyFormats[$formatData['@attributes']['numberSystem']] = ['alias' => $matches[1]];
            }
        } elseif (isset($formatData['currencyFormatLength']) && isset($formatData['@attributes']['numberSystem'])) {
            $currencyFormats[$formatData['@attributes']['numberSystem']] = [];

            if (isset($formatData['currencyFormatLength']['currencyFormat'])) {
                $handleSingleCurrencyFormatLength($formatData['currencyFormatLength'], $currencyFormats[$formatData['@attributes']['numberSystem']]);
            } else {
                foreach ($formatData['currencyFormatLength'] as $currencyFormatLength) {
                    $handleSingleCurrencyFormatLength($currencyFormatLength, $currencyFormats[$formatData['@attributes']['numberSystem']]);
                }
            }
        }
    };

    $currencyFormats = [];

    if (isset($formatsData['@attributes'])) {
        $handleSingleNumberingSystemCurrencyFormat($formatsData, $currencyFormats);
    } else {
        foreach ($formatsData as $formatData) {
            $handleSingleNumberingSystemCurrencyFormat($formatData, $currencyFormats);
        }
    }

    saveJsonFile($currencyFormats, $destinationDir . DIRECTORY_SEPARATOR . 'number.currencies.json', JSON_FORCE_OBJECT);
}

/**
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
    $data = [];

    $handleSingleRowData = function ($rawRowData, $type, &$data) {
        if (!isset($rawRowData['@value']) || !isset($rawRowData['@attributes']['type'])) {
            throw new Exception("Bad $type data detected!");
        } else {
            $code = $rawRowData['@attributes']['type'];
            $name = $rawRowData['@value'];

            $data[$code] = $name;
        }
    };

    if (!isset($rawData['@value'])) {
        foreach ($rawData as $rawRowData) {
            $handleSingleRowData($rawRowData, $type, $data);
        }
    } else {
        $handleSingleRowData($rawData, $type, $data);
    }

    saveJsonFile($data, $destinationDir . DIRECTORY_SEPARATOR . $fileName, JSON_FORCE_OBJECT);
}

/**
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
            handleSingleLocaleDataIdentity($localeData['ldml']['identity'], $localeDirectory);

            if (isset($localeData['ldml']['localeDisplayNames']['territories']['territory'])) {
                handleSingleLocaleDataSimpleNames('territory', $localeData['ldml']['localeDisplayNames']['territories']['territory'], $localeDirectory, 'territory.names.json');
            }

            if (isset($localeData['ldml']['localeDisplayNames']['languages']['language'])) {
                handleSingleLocaleDataSimpleNames('language', $localeData['ldml']['localeDisplayNames']['languages']['language'], $localeDirectory, 'language.names.json');
            }

            if (isset($localeData['ldml']['localeDisplayNames']['scripts']['script'])) {
                handleSingleLocaleDataSimpleNames('script', $localeData['ldml']['localeDisplayNames']['scripts']['script'], $localeDirectory, 'script.names.json');
            }

            if (isset($localeData['ldml']['numbers']['currencies']['currency'])) {
                handleSingleLocaleDataCurrencies($localeData['ldml']['numbers']['currencies']['currency'], $localeDirectory);
            }

            if (isset($localeData['ldml']['numbers']['symbols'])) {
                handleSingleLocaleDataSymbols($localeData['ldml']['numbers']['symbols'], $localeDirectory);
            }

            if (isset($localeData['ldml']['numbers']['currencyFormats'])) {
                handleSingleLocaleDataCurrencyFormats($localeData['ldml']['numbers']['currencyFormats'], $localeDirectory);
            }
        }

    } else {
        throw new Exception("Failed to get \"$locale\" locale data");
    }
}

/**
 * Read an xml file and get its contents as array
 *
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
 * Process xml data file with processing handlers
 *
 * @param $fileName
 * @param array $handlers
 * @throws Exception
 */
function processDataFileWithHandlers($fileName, $handlers = [])
{
    if ($fileName) {
        if ($data = getXmlDataFileContentsAsArray($fileName)) {
            foreach ($handlers as $handler) {
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
        $dirs = [
            'common/main' => 'main',
            'common/supplemental' => 'supplemental',
        ];

        foreach ($dirs as $source => $target) {
            echo "Checking out the CLDR $target repository (this may take a while)... \n";

            $output = [];
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

/**
 * Build specific data for locales (every or only most popular)
 */
function buildLocaleSpecificData()
{
    echo "Determining the list of the available locales... ";

    $availableLocales = [];
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

        $locales = array_merge(DEFAULT_LOCALES, ['root']);
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
        handleSingleLocaleData($locale, $localesDirectory . DIRECTORY_SEPARATOR . str_replace('-', '_', $locale) . ".xml");

        showStatus($key + 1, $overallCount, " Locale \"$locale\"", 50);
    }

    enableOutput();

    echo "Done.\n";
}

/**
 * Build supplemental data for CLDR
 */
function buildSupplementalData()
{
    $supplementalDataFile = LOCAL_VCS_DIR . str_replace("/", DIRECTORY_SEPARATOR, "/supplemental/supplementalData.xml");
    $supplementalMetaDataFile = LOCAL_VCS_DIR . str_replace("/", DIRECTORY_SEPARATOR, "/supplemental/supplementalMetadata.xml");
    $numberingSystemsDataFile = LOCAL_VCS_DIR . str_replace("/", DIRECTORY_SEPARATOR, "/supplemental/numberingSystems.xml");
    $likelySubtagsDataFile = LOCAL_VCS_DIR . str_replace("/", DIRECTORY_SEPARATOR, "/supplemental/likelySubtags.xml");

    $dataHandlers = [
        'supplemental' => [
            $supplementalDataFile => [
                'handleGeneralCurrencyData',
                'handleGeneralTerritoryInfoData',
                'handleGeneralTerritoryContainmentData',
                'handleGeneralTerritoryMapping',
                'handleGeneralCurrencyMapping',
                'handleGeneralParentLocales'
            ],
            $supplementalMetaDataFile => [
                'handleLanguageAlias',
                'handleTerritoryAlias',
            ]
        ],
        'likelySubtags' => [
            $likelySubtagsDataFile => [
                'handleLikelySubtagsData'
            ]
        ],
        'numeric' => [
            $numberingSystemsDataFile => [
                'handleNumberingSystemsData'
            ]
        ]
    ];

    foreach ($dataHandlers as $dataCategory => $handlersPerFile) {
        if ($handlersPerFile) {
            echo "Building $dataCategory data... \n";

            foreach ($handlersPerFile as $fileName => $handlers) {
                processDataFileWithHandlers($fileName, $handlers);
            }

            echo "$dataCategory data was built. \n";
        }
    }
}

/**
 * Build CLDR json data
 *
 * @throws Exception
 */
function buildCLDRJson()
{
    buildSupplementalData();

    buildLocaleSpecificData();
}

/**
 * Put data into a json file
 *
 * @param $data
 * @param $file
 * @param int $jsonFlags
 * @throws Exception
 */
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

/**
 * Delete object specified by its path from the filesystem
 *
 * @param $path
 * @throws Exception
 */
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
        foreach (array_diff($contents, ['.', '..']) as $item) {
            deleteFromFilesystem($path . DIRECTORY_SEPARATOR . $item);
        }
        if (rmdir($path) === false) {
            throw new Exception("Failed to delete directory $path");
        }
    }
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
