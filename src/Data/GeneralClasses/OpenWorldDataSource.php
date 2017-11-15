<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Data\GeneralClasses;

use GinoPane\PhpOpenWorld\Data\GeneralClasses\SourceLoaderResults\Factories\JsonResultFactory;
use GinoPane\PhpOpenWorld\Data\GeneralClasses\SourceLoaders\FileSourceLoader;
use GinoPane\PhpOpenWorld\Entities\Locale;
use GinoPane\PhpOpenWorld\Data\Interfaces\SourceLoaderResultInterface;
use GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers\LocaleProvider;
use GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers\GeneralProvider;
use GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;

/**
 * Class OpenWorldDataSource
 *
 * PHP OpenWorld's specific data source
 *
 * @package GinoPane\PhpOpenWorld\Data\GeneralClasses
 */
class OpenWorldDataSource extends DataSource
{
    /**
     * Subdirectory used for data storage
     */
    const DATA_SUBDIRECTORY = 'data';

    /**
     * @var OpenWorldDataSource
     */
    private static $instance = null;

    /**
     * Gets OpenWorldDataSource instance for loading data sources
     *
     * @return OpenWorldDataSource
     */
    public static function getInstance(): OpenWorldDataSource
    {
        if (is_null(self::$instance)) {
            self::$instance = new self(
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

        return self::$instance;
    }

    /**
     * Load general sources
     *
     * @param string $uri
     *
     * @return array
     */
    public function loadGeneral(string $uri): array
    {
        return $this->load(
            $uri, // @codeCoverageIgnore
            new DataProviderCondition(
                GeneralProvider::getConditionKey()
            )
        )->asArray();
    }

    /**
     * Load locale-specific sources
     *
     * @param string $uri
     * @param Locale $locale
     *
     * @return array
     */
    public function loadLocaleSpecific(string $uri, Locale $locale): array
    {
        return $this->load(
            $uri,
            new DataProviderCondition(
                LocaleProvider::getConditionKey(),
                $locale
            )
        )->asArray();
    }

    /**
     * Get data directory
     *
     * @return string
     */
    public static function getDataDirectory(): string
    {
        return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
    }
}
