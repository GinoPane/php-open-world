<?php

use OpenWorld\Entities\Territory;
use OpenWorld\Exceptions\InvalidTerritoryCodeException;
use OpenWorld\Exceptions\InvalidTerritoryCodeTypeException;

/**
 * Class TerritoryTest
 */
class TerritoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_territory_for_valid_identifier()
    {
        $territory = new Territory('AA');

        $this->assertInstanceOf(Territory::class, $territory);
    }

    /**
     * @test
     *
     * @param $code
     * @param $codeType
     * @param array $expectedCodes
     * @param string $expectedOriginalCode
     *
     * @dataProvider getValidTerritoryCodes
     */
    public function it_checks_that_codes_filled_correctly(
        string $code,
        string $codeType,
        array $expectedCodes,
        string $expectedOriginalCode = null
    ) {
        $territory = new Territory($code, $codeType);

        if (is_null($expectedOriginalCode)) {
            $expectedOriginalCode = $code;
        }

        $this->assertEquals($expectedOriginalCode, $territory->getCode());
        $this->assertEquals($expectedOriginalCode, (string)$territory);

        foreach ($expectedCodes as $codeType => $codeValue) {
            $this->assertEquals($codeValue, $territory->getCodeByType($codeType));
        }
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_code_type()
    {
        $this->expectException(InvalidTerritoryCodeTypeException::class);

        $territory = new Territory('AA', 'Foo');

        $this->assertInstanceOf(Territory::class, $territory);
    }

    /**
     * @test
     * @dataProvider getInvalidTerritoryCodesToCodeTypeCombinations
     *
     * @param $code
     * @param $codeType
     */
    public function it_throws_exception_for_invalid_code(string $code, string $codeType)
    {
        $this->expectException(InvalidTerritoryCodeException::class);

        $territory = new Territory($code, $codeType);

        $this->assertInstanceOf(Territory::class, $territory);
    }

    /**
     * @test
     * @dataProvider getTerritoryParentCodes
     *
     *
     * @param string $code
     * @param array $expectedParents
     * @param bool $expand
     */
    public function it_gets_territory_parent_codes(string $code, array $expectedParents, bool $expand = true)
    {
        $territory = new Territory($code);

        $this->assertEquals($expectedParents, $territory->getParentCodes($expand), '', 0, 10, true);
    }

    /**
     * @test
     * @dataProvider getTerritoryChildrenCodes
     *
     *
     * @param string $code
     * @param array $expectedChildren
     * @param bool $expand
     */
    public function it_gets_territory_children_codes(string $code, array $expectedChildren, bool $expand = false)
    {
        $territory = new Territory($code);

        $this->assertEquals($expectedChildren, $territory->getChildrenCodes($expand), '', 0, 10, true);
    }

    /**
     * @test
     *
     * @param $territoryCode
     * @param $expectedResult
     *
     * @dataProvider getVariousTerritoryCodes
     */
    public function it_validates_territory_code($territoryCode, $expectedResult)
    {
        $this->assertEquals(Territory::codeIsLikelyValid($territoryCode), $expectedResult);
    }


    /**
     * Returns valid territory codes and expected codes to be set
     *
     * @return array
     */
    public function getValidTerritoryCodes()
    {
        return [
            ['AA', '', [
                Territory::ISO_3166_A2  => 'AA',
                Territory::ISO_3166_A3  => 'AAA',
                Territory::ISO_3166_N   => '958',
                Territory::FIPS_10      => null,
                Territory::UNM_49       => null,
            ]],
            ['AA', Territory::FIPS_10, [
                Territory::ISO_3166_A2  => 'AW',
                Territory::ISO_3166_A3  => 'ABW',
                Territory::ISO_3166_N   => '533',
                Territory::FIPS_10      => 'AA',
                Territory::UNM_49       => null,
            ]],
            ['AV', '', [
                Territory::ISO_3166_A2  => 'AI',
                Territory::ISO_3166_A3  => 'AIA',
                Territory::ISO_3166_N   => '660',
                Territory::FIPS_10      => 'AV',
                Territory::UNM_49       => null,
            ]],
            ['AV', Territory::FIPS_10, [
                Territory::ISO_3166_A2  => 'AI',
                Territory::ISO_3166_A3  => 'AIA',
                Territory::ISO_3166_N   => '660',
                Territory::FIPS_10      => 'AV',
                Territory::UNM_49       => null,
            ]],
            ['155', '', [
                Territory::ISO_3166_A2  => null,
                Territory::ISO_3166_A3  => null,
                Territory::ISO_3166_N   => null,
                Territory::FIPS_10      => null,
                Territory::UNM_49       => '155',
            ]],
            ['XPP', Territory::ISO_3166_A3, [
                Territory::ISO_3166_A2  => 'XP',
                Territory::ISO_3166_A3  => 'XPP',
                Territory::ISO_3166_N   => '988',
                Territory::FIPS_10      => null,
                Territory::UNM_49       => null,
            ]],
            ['172', '', [
                Territory::ISO_3166_A2  => 'RU',
                Territory::ISO_3166_A3  => 'RUS',
                Territory::ISO_3166_N   => '643',
                Territory::FIPS_10      => 'RS',
                Territory::UNM_49       => null,
            ], 'RU'],
        ];
    }

    /**
     * Returns valid territory codes and expected codes to be set
     *
     * @return array
     */
    public function getInvalidTerritoryCodesToCodeTypeCombinations()
    {
        return [
            ['Foo', ''],
            ['AA', Territory::ISO_3166_A3],
            ['BLR', Territory::ISO_3166_A2]
        ];
    }

    /**
     * Provides script codes for validation
     *
     * @return array
     */
    public function getVariousTerritoryCodes()
    {
        return [
            ['001', true],
            ['BY', true],
            ['by', true],
            ['150', true],
            ['AAA', true],
            ['AA', true],
            ['1906', false],
            ['A', false],
        ];
    }

    /**
     * Returns valid territory parent codes
     *
     * @return array
     */
    public function getTerritoryParentCodes()
    {
        return [
            ['BY', ['151', 'UN'], false],
            ['BY', ['151', 'UN', '150', '001']],
            ['FR', ['001', '150', 'UN', 'EZ', 'EU', '155']],
            ['001', []],
        ];
    }

    /**
     * Returns valid territory parent codes
     *
     * @return array
     */
    public function getTerritoryChildrenCodes()
    {
        return [
            ['BY', []],
            ['BY', [], true],
            ['001', ['019', '002', '150', '142', '009']],
            ['001', array (
                'AC',
                'AD',
                'AE',
                'AF',
                'AG',
                'AI',
                'AL',
                'AM',
                'AO',
                'AQ',
                'AR',
                'AS',
                'AT',
                'AU',
                'AW',
                'AX',
                'AZ',
                'BA',
                'BB',
                'BD',
                'BE',
                'BF',
                'BG',
                'BH',
                'BI',
                'BJ',
                'BL',
                'BM',
                'BN',
                'BO',
                'BQ',
                'BR',
                'BS',
                'BT',
                'BV',
                'BW',
                'BY',
                'BZ',
                'CA',
                'CC',
                'CD',
                'CF',
                'CG',
                'CH',
                'CI',
                'CK',
                'CL',
                'CM',
                'CN',
                'CO',
                'CP',
                'CR',
                'CU',
                'CV',
                'CW',
                'CX',
                'CY',
                'CZ',
                'DE',
                'DG',
                'DJ',
                'DK',
                'DM',
                'DO',
                'DZ',
                'EA',
                'EC',
                'EE',
                'EG',
                'EH',
                'ER',
                'ES',
                'ET',
                'FI',
                'FJ',
                'FK',
                'FM',
                'FO',
                'FR',
                'GA',
                'GB',
                'GD',
                'GE',
                'GF',
                'GG',
                'GH',
                'GI',
                'GL',
                'GM',
                'GN',
                'GP',
                'GQ',
                'GR',
                'GS',
                'GT',
                'GU',
                'GW',
                'GY',
                'HK',
                'HM',
                'HN',
                'HR',
                'HT',
                'HU',
                'IC',
                'ID',
                'IE',
                'IL',
                'IM',
                'IN',
                'IO',
                'IQ',
                'IR',
                'IS',
                'IT',
                'JE',
                'JM',
                'JO',
                'JP',
                'KE',
                'KG',
                'KH',
                'KI',
                'KM',
                'KN',
                'KP',
                'KR',
                'KW',
                'KY',
                'KZ',
                'LA',
                'LB',
                'LC',
                'LI',
                'LK',
                'LR',
                'LS',
                'LT',
                'LU',
                'LV',
                'LY',
                'MA',
                'MC',
                'MD',
                'ME',
                'MF',
                'MG',
                'MH',
                'MK',
                'ML',
                'MM',
                'MN',
                'MO',
                'MP',
                'MQ',
                'MR',
                'MS',
                'MT',
                'MU',
                'MV',
                'MW',
                'MX',
                'MY',
                'MZ',
                'NA',
                'NC',
                'NE',
                'NF',
                'NG',
                'NI',
                'NL',
                'NO',
                'NP',
                'NR',
                'NU',
                'NZ',
                'OM',
                'PA',
                'PE',
                'PF',
                'PG',
                'PH',
                'PK',
                'PL',
                'PM',
                'PN',
                'PR',
                'PS',
                'PT',
                'PW',
                'PY',
                'QA',
                'RE',
                'RO',
                'RS',
                'RU',
                'RW',
                'SA',
                'SB',
                'SC',
                'SD',
                'SE',
                'SG',
                'SH',
                'SI',
                'SJ',
                'SK',
                'SL',
                'SM',
                'SN',
                'SO',
                'SR',
                'SS',
                'ST',
                'SV',
                'SX',
                'SY',
                'SZ',
                'TA',
                'TC',
                'TD',
                'TF',
                'TG',
                'TH',
                'TJ',
                'TK',
                'TL',
                'TM',
                'TN',
                'TO',
                'TR',
                'TT',
                'TV',
                'TW',
                'TZ',
                'UA',
                'UG',
                'UM',
                'US',
                'UY',
                'UZ',
                'VA',
                'VC',
                'VE',
                'VG',
                'VI',
                'VN',
                'VU',
                'WF',
                'WS',
                'XK',
                'YE',
                'YT',
                'ZA',
                'ZM',
                'ZW',
            ), true],
        ];
    }
}
