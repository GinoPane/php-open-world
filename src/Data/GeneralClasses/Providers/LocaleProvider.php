<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\GeneralClasses\Providers;

use Generator;
use OpenWorld\Data\AbstractClasses\DataProviderAbstract;
use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;
use OpenWorld\Data\GeneralClasses\OpenWorldDataSource as OWD;
use OpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;
use OpenWorld\Entities\Locale;

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
        //load locale alternatives

        //filter alternatives and leave only existing directories

        //load

        foreach ($this->getLocaleDirectory($condition->getLocale()) as $localeDirectory) {
            var_dump($localeDirectory);
        }
        echo "\n\n\n";

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

    /**
     * Gets existent locale directories
     *
     * @param Locale $locale
     *
     * @return Generator
     */
    private function getLocaleDirectory(Locale $locale): Generator
    {
        $alternativeLocales = $locale->getAlternativeCodes();

        array_reverse($alternativeLocales);

        foreach ($alternativeLocales as $locale) {
            $locale = str_replace("_", "-", $locale);

            $localeDirectory = $this->adjustUri($locale);

            if (is_dir($localeDirectory)) {
                yield $localeDirectory;
            }
        }
    }
}
