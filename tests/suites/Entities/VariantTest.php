<?php

use GinoPane\PhpOpenWorld\Entities\Variant;

/**
 * Class VariantTest
 */
class VariantTest extends PhpOpenWorldTestCase
{
    /**
     * @test
     *
     * @param $variantCode
     *
     * @dataProvider getValidVariantCodes
     */
    public function it_creates_variant_for_valid_identifier($variantCode)
    {
        $variant = new Variant($variantCode);

        $this->assertInstanceOf(Variant::class, $variant);
        $this->assertEquals(strtolower($variantCode), strtolower($variant->getCode()));
    }

    /**
     * @test
     *
     * @param $variantCode
     * @param $expectedResult
     *
     * @dataProvider getVariousVariantCodes
     */
    public function it_validates_variant_code($variantCode, $expectedResult)
    {
        $this->assertEquals(Variant::codeIsLikelyValid($variantCode), $expectedResult);
    }

    /**
     * Provides valid language codes data
     *
     * @return array
     */
    public function getValidVariantCodes()
    {
        return [
            ['POSIX'],
            ['TARASK'],
            ['1994'],
            ['BAKU1926'],
            ['VALENCIA']
        ];
    }

    /**
     * Provides variant codes for validation
     *
     * @return array
     */
    public function getVariousVariantCodes()
    {
        return [
            ['POSIX', true],
            ['TARASK', true],
            ['1994', true],
            ['BAKU1926', true],
            ['VALENCIA', true],
            ['953', false],
            ['Cyrl', false]
        ];
    }
}
