<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\GeneralClasses\Providers;

use OpenWorld\Data\GeneralClasses\OpenWorldDataSource as OWD;
use OpenWorld\Data\AbstractClasses\DataProviderAbstract;
use OpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;

/**
 * Class GeneralProvider
 *
 * @package OpenWorld\Data\GeneralClasses\Providers
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
     * @param DataProviderCondition $condition
     * @return string
     */
    public function adjustUri(string $uri, DataProviderCondition $condition): string
    {
        $generalDataSubdirectory = OWD::getDataDirectory() . self::GENERAL_DATA_SUBDIRECTORY . DIRECTORY_SEPARATOR;

        return $generalDataSubdirectory . $uri;
    }
}
