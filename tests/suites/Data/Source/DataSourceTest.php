<?php

use PHPUnit\Framework\TestCase;

use OpenWorld\Data\GeneralClasses\{
    DataSource,
    Providers\Conditions\DataProviderCondition,
    Providers\LocaleProvider,
    Providers\GeneralProvider,
    SourceLoaders\FileSourceLoader,
    SourceLoaderResults\Factories\JsonResultFactory
};

use OpenWorld\Entities\Locale;
use OpenWorld\Data\Interfaces\DataProviderInterface;
use OpenWorld\Collections\Interfaces\CollectionInterface;


/**
 * Class DataTest
 *
 * General low-level tests for data provider
 */
class DataTest extends TestCase
{
    const LOCALE_SPECIFIC_FILES_NUMBER_TO_TEST = 100;
    /**
     * @var string
     */
    protected $buildStatusFile = PROJECT_ROOT . '/data/status.json';

    /**
     * @var DataSource
     */
    public $dataSource = null;

    public function setUp()
    {
        $this->dataSource = new DataSource(
            new GeneralProvider(
                new FileSourceLoader(),
                new JsonResultFactory()
            ),
            new LocaleProvider(
                new FileSourceLoader(),
                new JsonResultFactory()
            )
        );
    }

    /**
     * @test
     */
    public function it_gets_providers_collection_and_checks_its_type()
    {
        $providers = $this->dataSource->providers();

        $this->assertInstanceOf(CollectionInterface::class, $providers);

        foreach($providers as $provider) {
            $this->assertInstanceOf(DataProviderInterface::class, $provider);
        }
    }

    /**
     *
     * @test
     *
     * @depends BuildTest::it_checks_that_supplemental_files_are_available
     */
    public function it_tries_to_load_with_unacceptable_condition()
    {
        $this->expectException(\OpenWorld\Exceptions\NoDataProvidersAvailableException::class);

        $fileName = "someFile";

        $this->assertStringEqualsFile($fileName, $this->dataSource->load(
            $fileName,
            new DataProviderCondition('someKey')
        )->getContent(), "Content's not equal to $fileName!");
    }

    /**
     * @param string $fileName
     * @param string $pathName
     *
     * @test
     * @dataProvider generalFilesProvider
     *
     * @depends      BuildTest::it_checks_that_supplemental_files_are_available
     */
    public function it_loads_general_files(string $fileName, string $pathName)
    {
        $this->assertStringEqualsFile($pathName, $this->dataSource->load(
            $fileName,
            new DataProviderCondition(GeneralProvider::getConditionKey())
        )->getContent(), "Content's not equal to $fileName!");
    }

    /**
     * @param string $fileName
     * @param string $pathName
     *
     * @test
     * @dataProvider generalFilesProvider
     *
     * @depends      BuildTest::it_checks_that_supplemental_files_are_available
     */
    public function it_loads_general_files_from_cache(string $fileName, string $pathName)
    {
        $this->dataSource->providers()->clear();

        $this->assertStringEqualsFile($pathName, $this->dataSource->load(
            $fileName,
            new DataProviderCondition(GeneralProvider::getConditionKey())
        )->getContent(), "Content's not equal to $fileName!");
    }

    /**
     * @param string $fileName
     * @param string $pathName
     *
     * @test
     * @dataProvider generalFilesProvider
     *
     * @depends      BuildTest::it_checks_that_supplemental_files_are_available
     */
    public function it_fails_to_load_files_after_cache_has_been_cleared_and_with_no_providers(
        string $fileName,
        string $pathName
    ) {
        $this->expectException(\OpenWorld\Exceptions\NoDataProvidersAvailableException::class);

        $this->dataSource->clearCache();

        $this->dataSource->providers()->clear();

        $this->assertStringEqualsFile($pathName, $this->dataSource->load(
            $fileName,
            new DataProviderCondition(GeneralProvider::getConditionKey())
        )->getContent(), "Content's not equal to $fileName!");
    }

    /**
     * @param string $localeCode
     * @param string $fileName
     * @param string $pathName
     *
     * @test
     * @dataProvider localeSpecificFilesProvider
     *
     */
    public function it_loads_locale_specific_files(string $localeCode, string $fileName, string $pathName)
    {
        $locale = Locale::fromString($localeCode);

        var_dump($locale->getCode());

        $this->assertTrue(true);
    }

    /**
     * Returns general files from supplemental directory
     *
     * @return Generator
     */
    public function generalFilesProvider(): Generator
    {
        $buildStatus = json_decode(file_get_contents($this->buildStatusFile), true);

        $generalDataDirectory = PROJECT_ROOT . $buildStatus['data']['supplemental']['directory']. DIRECTORY_SEPARATOR;

        $directory = new DirectoryIterator($generalDataDirectory);

        foreach ($directory as $file) {
            if ($file->isFile() && $file->isReadable()) {
                yield [$file->getFilename(), $file->getPathname()];
            }
        }
    }

    /**
     * Return random locale-specific files
     *
     * @return Generator Locale key, file name and path name
     */
    public function localeSpecificFilesProvider(): Generator
    {
        static $filesToLoad = self::LOCALE_SPECIFIC_FILES_NUMBER_TO_TEST;

        $buildStatus = json_decode(file_get_contents($this->buildStatusFile), true);

        $localeDataDirectory = PROJECT_ROOT . $buildStatus['data']['locales']['directory'];

        $localeList = $buildStatus['data']['locales']['file-list'];

        while ($filesToLoad--) {
            $randomLocaleKey = array_rand($localeList);

            $randomFileKey = array_rand($localeList[$randomLocaleKey]);

            $fileName = $localeList[$randomLocaleKey][$randomFileKey];

            yield [
                $randomLocaleKey,
                $fileName,
                $localeDataDirectory . DIRECTORY_SEPARATOR. $randomLocaleKey . DIRECTORY_SEPARATOR . $fileName
            ];
        }
    }
}
