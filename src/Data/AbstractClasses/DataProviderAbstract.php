<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\DataProviderInterface;
use OpenWorld\Data\Interfaces\SourceLoaderInterface;
use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;
use OpenWorld\Data\Interfaces\SourceLoaderResultFactoryInterface;
use OpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;

/**
 * Class ProviderAbstract
 *
 * Starting point for data provider classes
 *
 * @package OpenWorld\Data\AbstractClasses
 */
abstract class DataProviderAbstract implements DataProviderInterface
{

    /**
     * Source loader instance
     *
     * @var SourceLoaderInterface
     */
    protected $loader = null;

    /**
     * Represents result of source loading
     *
     * @var SourceLoaderResultFactoryInterface
     */
    protected $resultFactory = '';

    /**
     * Condition key for accept matching
     *
     * @var string
     */
    protected static $conditionKey = __CLASS__;

    /**
     * ProviderAbstract constructor
     *
     * @param SourceLoaderInterface $loader
     * @param SourceLoaderResultFactoryInterface $resultFactory
     *
     * return void
     */
    public function __construct(SourceLoaderInterface $loader, SourceLoaderResultFactoryInterface $resultFactory)
    {
        $this->setLoader($loader);
        $this->setResultFactory($resultFactory);
    }

    /**
     * Set provider's source loader
     *
     * @param SourceLoaderInterface $loader
     *
     * return void
     */
    public function setLoader(SourceLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Get provider's source loader
     *
     * @return SourceLoaderInterface
     */
    public function getLoader(): SourceLoaderInterface
    {
        return $this->loader;
    }

    /**
     * Set the factory for source loader results
     *
     * @param SourceLoaderResultFactoryInterface $resultFactory
     */
    public function setResultFactory(SourceLoaderResultFactoryInterface $resultFactory)
    {
        $this->resultFactory = $resultFactory;
    }

    /**
     * Returns the source loader results factory
     *
     * @return SourceLoaderResultFactoryInterface
     */
    public function getResultFactory(): SourceLoaderResultFactoryInterface
    {
        return $this->resultFactory;
    }

    /**
     * Loads data specified by URI using providers selected by conditions
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
     * Checks if provider accepts the condition
     *
     * @param DataProviderCondition $condition
     *
     * @return bool
     */
    public function accept(DataProviderCondition $condition): bool
    {
        if ($this->getConditionKey() == $condition->getKey()) {
            return true;
        }

        return false;
    }

    /**
     * Returns the condition key for current provider
     *
     * @return string
     */
    public static function getConditionKey(): string
    {
        return static::$conditionKey;
    }

    /**
     * Make URI appropriate for current provider
     *
     * @param string $uri
     *
     * @return string
     */
    abstract protected function adjustUri(string $uri): string;
}
