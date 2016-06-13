<?php

use OpenWorld\Data\DataFile;

class DataTest extends PHPUnit_Framework_TestCase
{
    protected $localDataProvider = null;
    protected $globalDataProvider = null;

    protected function setUp()
    {
        $this->localDataProvider = new DataFile();
        $this->globalDataProvider = new DataFile();

        //$this->markTestSkipped('This test has not been implemented yet.');
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
        var_dump($locale, $fileName);
    }
}
