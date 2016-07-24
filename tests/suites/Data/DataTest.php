<?php

use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    protected function setUp()
    {

    }

    public function generalFilesProvider()
    {
        return [
            [
                'currency.codes.json'
            ],
            [
                'currency.fractions.json'
            ],
            [
                'language.alias.json'
            ],
            [
                'language.territories.json'
            ],
            [
                'likely.subtags.json'
            ],
            [
                'number.systems.json'
            ],
            [
                'territory.alias.json'
            ],
            [
                'territory.codes.json'
            ],
            [
                'territory.containment.json'
            ],
            [
                'territory.currencies.json'
            ],
            [
                'territory.info.json'
            ]
        ];
    }

    public function localeSpecificFilesProvider()
    {
        return [
            [
                'en', 'currency.names.json'
            ],
            [
                'en', 'identity.json'
            ],
            [
                'en', 'language.names.json'
            ],
            [
                'en', 'number.currencies.json'
            ],
            [
                'en', 'number.symbols.json'
            ],
            [
                'en', 'script.names.json'
            ],
            [
                'en', 'territory.names.json'
            ]
        ];
    }

    /**
     * @param string $fileName
     *
     * @test
     * @dataProvider generalFilesProvider
     */
    public function testLoadGeneralFiles($fileName)
    {
        $this->assertTrue(true);
    }

    /**
     * @param string $locale
     * @param string $fileName
     *
     * @test
     * @dataProvider localeSpecificFilesProvider
     */
    public function testLoadLocaleSpecificFiles($locale, $fileName)
    {
        $this->assertTrue(true);
    }
}
