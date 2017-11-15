<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Data\GeneralClasses;

use GinoPane\PhpOpenWorld\Data\Interfaces\DataSourceInterface;
use GinoPane\PhpOpenWorld\Data\Interfaces\DataProviderInterface;
use GinoPane\PhpOpenWorld\Assertions\GeneralClasses\TypeAssertion;
use GinoPane\PhpOpenWorld\Collections\Interfaces\CollectionInterface;
use GinoPane\PhpOpenWorld\Data\Interfaces\SourceLoaderResultInterface;
use GinoPane\PhpOpenWorld\Exceptions\NoDataProvidersAvailableException;
use GinoPane\PhpOpenWorld\Collections\GeneralClasses\AssertionStrictCollection;
use GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;

/**
 * Class DataSource
 *
 * @package GinoPane\PhpOpenWorld\Data\GeneralClasses
 */
class DataSource implements DataSourceInterface
{

    /**
     * Cached data sources by URI and condition key
     *
     * @var array
     */
    private static $cache = [];

    /**
     * Providers registered for current DataSource
     *
     * @var CollectionInterface
     */
    private $providers = null;

    /**
     * DataSource constructor
     *
     * @param DataProviderInterface[] ...$providers
     */
    public function __construct(DataProviderInterface ...$providers)
    {
        $this->providers = new AssertionStrictCollection(
            new TypeAssertion(DataProviderInterface::class, TypeAssertion::CLASS_IMPLEMENTS_TYPE),
            $providers
        );
    }

    /**
     * Loads data specified by URI using providers selected by conditions
     *
     * @param string $uri
     * @param DataProviderCondition $condition
     *
     * @return SourceLoaderResultInterface
     *
     * @throws NoDataProvidersAvailableException
     */
    public function load(string $uri, DataProviderCondition $condition): SourceLoaderResultInterface
    {
        $cacheKey = $this->getCacheKey($uri, $condition);

        if (!empty(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        /* @var DataProviderInterface $provider */
        foreach ($this->providers() as $provider) {
            if ($provider->accept($condition)) {
                self::$cache[$cacheKey] = $provider->load($uri, $condition);

                return self::$cache[$cacheKey];
            }
        }

        throw new NoDataProvidersAvailableException($uri, $condition);
    } //@codeCoverageIgnore

    /**
     * Get DataSource's providers, registered for data loading
     *
     * @return CollectionInterface
     */
    public function providers(): CollectionInterface
    {
        return $this->providers;
    }

    /**
     * Clears static cache
     */
    public function clearCache(): void
    {
        self::$cache = [];
    }

    /**
     * Get cache key for uri and condition
     *
     * @param string $uri
     * @param DataProviderCondition $condition
     *
     * @return string
     */
    private function getCacheKey(string $uri, DataProviderCondition $condition): string
    {
        return sprintf("%1s_%2s", $uri, md5((string)$condition));
    }
}
