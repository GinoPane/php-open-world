<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld;

use GinoPane\PhpOpenWorld\Data\GeneralClasses\OpenWorldDataSource;
use GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers\GeneralProvider;
use GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers\LocaleProvider;
use GinoPane\PhpOpenWorld\Data\GeneralClasses\SourceLoaderResults\Factories\JsonResultFactory;
use GinoPane\PhpOpenWorld\Data\GeneralClasses\SourceLoaders\FileSourceLoader;

/**
 * Class PhpOpenWorld
 *
 * Entry point for PHP OpenWorld data usage
 *
 * @package GinoPane\PhpOpenWorld
 */
class PhpOpenWorld
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