<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\GeneralClasses;

use OpenWorld\Data\GeneralClasses\SourceLoaderResults\Factories\JsonResultFactory;
use OpenWorld\Data\GeneralClasses\SourceLoaders\FileSourceLoader;
use OpenWorld\Entities\Locale;
use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;
use OpenWorld\Data\GeneralClasses\Providers\LocaleProvider;
use OpenWorld\Data\GeneralClasses\Providers\GeneralProvider;
use OpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;

/**
 * Class OpenWorldDataSource
 *
 * PHP OpenWorld's specific data source
 *
 * @package OpenWorld\Data\GeneralClasses
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
