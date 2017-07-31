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
     *
     * @dataProvider getValidTerritoryCodes
     */
    public function it_checks_that_codes_filled_correctly($code, $codeType, array $expectedCodes)
    {
        $territory = new Territory($code, $codeType);

        $this->assertEquals($code, $territory->getCode());

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
    public function it_throws_exception_for_invalid_code($code, $codeType)
    {
        $this->expectException(InvalidTerritoryCodeException::class);

        $territory = new Territory($code, $codeType);

        $this->assertInstanceOf(Territory::class, $territory);
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
            ['XPP', '', [
                Territory::ISO_3166_A2  => 'XP',
                Territory::ISO_3166_A3  => 'XPP',
                Territory::ISO_3166_N   => '988',
                Territory::FIPS_10      => null,
                Territory::UNM_49       => null,
            ]],
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
}
