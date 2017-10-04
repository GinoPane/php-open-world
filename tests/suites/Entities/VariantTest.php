<?php

use OpenWorld\Entities\Variant;

/**
 * Class VariantTest
 */
class VariantTest extends OpenWorldTestCase
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
     * Provides valid language codes data
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
}
