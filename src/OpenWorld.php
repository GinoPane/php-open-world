<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld;

use OpenWorld\Data\GeneralClasses\OpenWorldDataSource;
use OpenWorld\Data\GeneralClasses\Providers\GeneralProvider;
use OpenWorld\Data\GeneralClasses\Providers\LocaleProvider;
use OpenWorld\Data\GeneralClasses\SourceLoaderResults\Factories\JsonResultFactory;
use OpenWorld\Data\GeneralClasses\SourceLoaders\FileSourceLoader;

/**
 * Class OpenWorld
 *
 * Entry point for PHP OpenWorld data usage
 *
 * @package OpenWorld
 */
class OpenWorld
{

    /**
     * Fallback locale to be used when no other locales work
     */
    const FALLBACK_LOCALE_CODE  = "en_US";

    /**
     * Origin root for all locales
     */
    const ROOT_LOCALE_CODE      = "root";

    /**
     * DataSource instance
     *
     * @var OpenWorldDataSource
     */
    private static $dataSource = null;

    public static function get()
    {

    }

    /**
     * Gets DataSource instance for loading data sources
     *
     * @return OpenWorldDataSource
     */
    public static function getDataSourceLoader(): OpenWorldDataSource
    {
        if (!self::$dataSource) {
            self::$dataSource = new OpenWorldDataSource(
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

        return self::$dataSource;
    }
}