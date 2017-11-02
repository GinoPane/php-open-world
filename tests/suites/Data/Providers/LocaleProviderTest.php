<?php

use OpenWorld\Data\GeneralClasses\Providers\LocaleProvider;
use PHPUnit\Framework\TestCase;

use OpenWorld\Data\GeneralClasses\Providers\GeneralProvider;
use OpenWorld\Data\GeneralClasses\SourceLoaders\FileSourceLoader;
use OpenWorld\Data\GeneralClasses\SourceLoaderResults\Factories\JsonResultFactory;
use OpenWorld\Data\Interfaces\SourceLoaderInterface;
use OpenWorld\Data\Interfaces\SourceLoaderResultFactoryInterface;
use OpenWorld\Entities\Locale;

/**
 * Class LocaleProviderTest
 */
class LocaleProviderTest extends TestCase
{
    /**
     * @var LocaleProvider
     */
    public $provider = null;

    public function setUp()
    {
        $this->provider = new LocaleProvider(
            new FileSourceLoader(),
            new JsonResultFactory()
        );
    }

    /**
     * @test
     */
    public function it_checks_get_result_factory_class()
    {
        $this->assertInstanceOf(SourceLoaderResultFactoryInterface::class, $this->provider->getResultFactory());
    }

    /**
     * @test
     */
    public function it_checks_get_loader_return_type()
    {
        $this->assertInstanceOf(SourceLoaderInterface::class, $this->provider->getLoader());
    }

    /**
     * @param string $localeCode
     * @param array $expectedDirectories
     *
     * @test
     * @dataProvider localeDirectoriesProvider
     */
    public function it_gets_a_list_of_locale_directories(string $localeCode, array $expectedDirectories = array())
    {
        $locale = Locale::fromString($localeCode);

        $directories = [];

        foreach ($this->provider->getLocaleDirectory($locale) as $directory) {
            $directories[] = basename($directory);
        }

        $this->assertEquals($expectedDirectories, $directories);
    }

    public function localeDirectoriesProvider()
    {
        return [
            [
                'ru',
                [
                    'ru-RU',
                    'ru',
                    'en-US',
                    'en-001',
                    'en',
                ]
            ],
            [
                'en',
                [
                    'en-US',
                    'en-001',
                    'en',
                ]
            ],
            [
                'es',
                [
                    'es-ES',
                    'es',
                    'en-US',
                    'en-001',
                    'en',
                ]
            ],
            [
                'it',
                [
                    'it-IT',
                    'it',
                    'en-US',
                    'en-001',
                    'en',
                ]
            ]
        ];
    }
}
