<?php

use OpenWorld\DataFile;

class DataTest extends PHPUnit_Framework_TestCase
{
    protected $dataProvider = null;

    protected function setUp()
    {
        $this->dataProvider = new DataFile();

        $this->markTestSkipped('This test has not been implemented yet.');
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
                'currency.names.json'
            ],
            [
                'identity.json'
            ],
            [
                'language.names.json'
            ],
            [
                'number.currencies.json'
            ],
            [
                'number.symbols.json'
            ],
            [
                'script.names.json'
            ],
            [
                'territory.names.json'
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

    }
}
