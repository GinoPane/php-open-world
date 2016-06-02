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

    }

    public function localeSpecificFilesProvider()
    {

    }

    /**
     * @param string $fileName
     *
     * @dataProvider generalFilesProvider
     */
    public function testLoadGeneralFiles($fileName)
    {

    }

    /**
     * @param string $locale
     * @param string $fileName
     *
     * @dataProvider localeSpecificFilesProvider
     */
    public function testLoadLocaleSpecificFiles($locale, $fileName)
    {

    }
}
