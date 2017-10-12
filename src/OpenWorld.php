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

    public static function get()
    {

    }
}