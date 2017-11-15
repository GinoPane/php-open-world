<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers;

use GinoPane\PhpOpenWorld\Data\GeneralClasses\OpenWorldDataSource as OWD;
use GinoPane\PhpOpenWorld\Data\AbstractClasses\DataProviderAbstract;

/**
 * Class GeneralProvider
 *
 * @package GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers
 */
class GeneralProvider extends DataProviderAbstract
{
    /**
     * Data subdirectory for general data
     */
    const GENERAL_DATA_SUBDIRECTORY = 'general';

    /**
     * Condition key for accept matching
     *
     * @var string
     */
    protected static $conditionKey = 'General';

    /**
     * Make URI appropriate for general provider
     *
     * @param string $uri
     *
     * @return string
     */
    public function adjustUri(string $uri): string
    {
        $dataSubdirectory = OWD::getDataDirectory() . self::GENERAL_DATA_SUBDIRECTORY . DIRECTORY_SEPARATOR;

        return $dataSubdirectory . $uri;
    }
}
