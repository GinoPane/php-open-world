<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\GeneralClasses\Providers;

use OpenWorld\Data\AbstractClasses\DataProviderAbstract;
use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;
use OpenWorld\Data\GeneralClasses\OpenWorldDataSource as OWD;
use OpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;

/**
 * Class LocaleProvider
 *
 * @package OpenWorld\Data\GeneralClasses\Providers
 */
class LocaleProvider extends DataProviderAbstract
{
    /**
     * Data subdirectory for locale data
     */
    const LOCALE_DATA_SUBDIRECTORY = 'locales';

    /**
     * Condition key for accept matching
     *
     * @var string
     */
    protected static $conditionKey = 'Locale';

    /**
     * Loads locale-specific data specified by URI
     *
     * @param string $uri Path to the resource to load
     * @param DataProviderCondition $condition Conditions that should be accepted while loading data
     *
     * @return SourceLoaderResultInterface
     */
    public function load(string $uri, DataProviderCondition $condition): SourceLoaderResultInterface
    {
        $result = $this->getResultFactory()->get();

        $result->setContent(
            $this->getLoader()->loadSource(
                $this->adjustUri($uri)
            )
        );

        return $result;
    }

    /**
     * Make URI appropriate for locale provider
     *
     * @param string $uri
     *
     * @return string
     */
    public function adjustUri(string $uri): string
    {
        $dataSubdirectory = OWD::getDataDirectory() . self::LOCALE_DATA_SUBDIRECTORY . DIRECTORY_SEPARATOR;

        return $dataSubdirectory . $uri;
    }
}
