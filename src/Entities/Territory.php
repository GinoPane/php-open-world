<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities;

use Closure;
use OpenWorld\Data\GeneralClasses\OpenWorldDataSource;
use OpenWorld\Entities\AbstractClasses\EntityAbstract;
use OpenWorld\Exceptions\InvalidTerritoryCodeException;
use OpenWorld\Exceptions\InvalidTerritoryCodeTypeException;

/**
 * Class Territory
 *
 * @package OpenWorld\Entities
 */
class Territory extends EntityAbstract
{
    /**
     * Source URI which is a storage for assertion data
     *
     * @var string
     */
    protected $keySourceUri = 'territory.codes.json';

    /**
     * Territory code types
     */
    const ISO_3166_A2   = 'iso3166alpha2';
    const ISO_3166_A3   = 'iso3166alpha3';
    const ISO_3166_N    = 'iso3166numeric';
    const FIPS_10       = 'fips10';
    const UNM_49        = 'unm49';

    /**
     * Valid code types for Territory
     *
     * @var array
     */
    private static $validCodeTypes = [
        self::ISO_3166_A2,
        self::ISO_3166_A3,
        self::ISO_3166_N,
        self::FIPS_10,
        self::UNM_49
    ];

    /**
     * Type of the code that is was used to create the instance
     *
     * @var string
     */
    private $originCodeType = '';

    /**
     * ISO 3166-1 alpha 2 territory code
     *
     * @link https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
     * @var string
     */
    private $iso3166alpha2 = '';

    /**
     * ISO 3166-1 alpha 3 territory code
     *
     * @link https://en.wikipedia.org/wiki/ISO_3166-1_alpha-3
     * @var string
     */
    private $iso3166alpha3 = '';

    /**
     * ISO 3166-1 numeric territory code. Not to mess up with UN M.49. Their numbers do not intersect.
     *
     * @link https://en.wikipedia.org/wiki/ISO_3166-1_numeric
     * @link https://en.wikipedia.org/wiki/UN_M.49
     * @var string
     */
    private $iso3166numeric = '';

    /**
     * FIPS 10 territory code
     *
     * @link https://en.wikipedia.org/wiki/List_of_FIPS_country_codes
     * @link https://en.wikipedia.org/wiki/Federal_Information_Processing_Standards
     *
     * @var string
     */
    private $fips10 = '';

    /**
     * UN M.49 territory code
     *
     * @link https://en.wikipedia.org/wiki/UN_M.49
     * @var string
     */
    private $unm49 = '';

    /**
     * Territory constructor
     *
     * @param string $code  Can be one of the following codes: ISO 3166-1 Alpha 2, ISO 3166-1 Alpha 3, ISO 3166-1 Numeric,
     *                      FIPS 10, UM M.49 (which includes ISO 3166-1 Numeric as a subset). Please note, that FIPS 10 codes
     *                      have intersections with ISO 3166-1 Alpha 2, in this case code would be treated as ISO 3166-1 Alpha 2
     *
     * @param string $codeType
     */
    public function __construct(string $code, string $codeType = '')
    {
        $this->assertCode($code, $this->getDataSourceLoader(), function($code, $source) use ($codeType) {
            return $this->fillTerritoryCodes($code, $codeType, $source);
        });
    }

    /**
     * Asserts that the code value is valid (exists within the source)
     *
     * @param string $code
     * @param OpenWorldDataSource $dataSource
     * @param Closure|null $keyPredicate
     *
     * @return void
     */
    public function assertCode(string $code, OpenWorldDataSource $dataSource, Closure $keyPredicate = null): void
    {
        list(
            self::ISO_3166_A2   => $this->iso3166alpha2,
            self::ISO_3166_A3   => $this->iso3166alpha3,
            self::ISO_3166_N    => $this->iso3166numeric,
            self::FIPS_10       => $this->fips10,
            self::UNM_49        => $this->unm49
        ) = $this->getAssertedCode($code, $dataSource, $keyPredicate);
    }

    /**
     * Asserts that code type is valid
     *
     * @param string $codeType
     *
     * @throws InvalidTerritoryCodeTypeException
     */
    protected function assertCodeType(string $codeType)
    {
        if (!in_array($codeType, self::$validCodeTypes) || !property_exists($this, $codeType)) {
            throw new InvalidTerritoryCodeTypeException($codeType);
        }
    }

    /**
     * Get the code for original code type
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->getCodeByType($this->originCodeType);
    }

    /**
     * Get the code by supplied code type
     *
     * @param string $type
     * @return null|string
     */
    public function getCodeByType(string $type = self::ISO_3166_A2): ?string
    {
        $this->assertCodeType($type);

        return $this->{$type};
    }

    /**
     * Assert code and code type and try to fill all other codes from the source
     *
     * @param string $code
     * @param string $codeType
     * @param array $source
     * @throws InvalidTerritoryCodeException
     * @return array
     */
    protected function fillTerritoryCodes(string $code, string $codeType, array $source)
    {
        $code = strtoupper($code);

        $returnCodes = [
            self::ISO_3166_A2   => null,
            self::UNM_49        => null,
            self::ISO_3166_A3   => null,
            self::ISO_3166_N    => null,
            self::FIPS_10       => null
        ];

        $sourceKeysToCheck = [
            self::ISO_3166_A2,
            self::UNM_49,
            self::ISO_3166_A2 . "_to_" .self::ISO_3166_A3,
            self::ISO_3166_A2 . "_to_" .self::ISO_3166_N,
            self::ISO_3166_A2 . "_to_" .self::FIPS_10
        ];

        //detect codeType by looking through the source
        if (!$codeType) {
            foreach($sourceKeysToCheck as $key) {
                if (!empty($source[$key]) && in_array($code, $source[$key])) {
                    $keyParts = explode('_', $key);
                    $codeType = end($keyParts);
                    break;
                }
            }

            if (!$codeType) {
                throw new InvalidTerritoryCodeException($code);
            }
        } else {
            $this->assertCodeType($codeType);
            //we need to check that provided code exists within the codeType
            if (!@in_array($code, $source[$codeType]) && !@in_array($code, $source[self::ISO_3166_A2 . "_to_" . $codeType])) {
                throw new InvalidTerritoryCodeException($code, $codeType);
            }
        }

        $this->originCodeType = $codeType;

        if ($codeType == self::UNM_49) {
            $returnCodes[$codeType] = $code;
        } else {
            $returnCodes[$codeType] = $code;

            if ($codeType !== self::ISO_3166_A2) {
                $returnCodes[self::ISO_3166_A2] =
                    ($codeFound = array_search($code, $source[self::ISO_3166_A2 . "_to_" . $codeType])) ? $codeFound : null;
            }

            if ($returnCodes[self::ISO_3166_A2]) {
                $knownCode = $returnCodes[self::ISO_3166_A2];

                if (empty($returnCodes[self::ISO_3166_A3])) {
                    $returnCodes[self::ISO_3166_A3] =
                        ($source[self::ISO_3166_A2 . "_to_" .self::ISO_3166_A3][$knownCode]) ?? null;
                }

                if (empty($returnCodes[self::ISO_3166_N])) {
                    $returnCodes[self::ISO_3166_N] =
                        ($source[self::ISO_3166_A2 . "_to_" .self::ISO_3166_N][$knownCode]) ?? null;
                }

                if (empty($returnCodes[self::FIPS_10])) {
                    $returnCodes[self::FIPS_10] =
                        ($source[self::ISO_3166_A2 . "_to_" .self::FIPS_10][$knownCode]) ?? null;
                }
            }
        }

        return $returnCodes;
    }
}