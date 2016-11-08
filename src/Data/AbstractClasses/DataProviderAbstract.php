<?php

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;
use OpenWorld\Data\Interfaces\DataProviderInterface;
use OpenWorld\Data\Interfaces\SourceLoaderInterface;
use OpenWorld\Data\Interfaces\SourceLoaderResultFactoryInterface;
use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;

/**
 * Class ProviderAbstract
 *
 * Starting point for data provider classes.
 *
 * @package OpenWorld\Data\AbstractClasses
 */
abstract class DataProviderAbstract implements DataProviderInterface
{

    /**
     * Source loader instance.
     *
     * @var SourceLoaderInterface
     */
    protected $loader = null;

    /**
     * Represents result of source loading.
     *
     * @var SourceLoaderResultFactoryInterface
     */
    protected $resultFactory = '';

    /**
     * Condition key for accept matching
     *
     * @var string
     */
    protected static $conditionKey = '';

    /**
     * ProviderAbstract constructor.
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
     * Set provider's source loader.
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
     * Get provider's source loader.
     *
     * @return SourceLoaderInterface
     */
    public function getLoader() : SourceLoaderInterface
    {
        return $this->loader;
    }

    /**
     * @param SourceLoaderResultFactoryInterface $resultFactory
     */
    public function setResultFactory(SourceLoaderResultFactoryInterface $resultFactory)
    {
        $this->resultFactory = $resultFactory;
    }

    /**
     * @return SourceLoaderResultFactoryInterface
     */
    public function getResultFactory() : SourceLoaderResultFactoryInterface
    {
        return $this->resultFactory;
    }

    /**
     * @inheritdoc
     */
    public function load(string $uri = '', DataProviderCondition $condition = null) : SourceLoaderResultInterface
    {
        $result = $this->getResultFactory()->get();

        $result->setContent(
            $this->getLoader()->loadSource(
                $this->adjustUri($uri, $condition)
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
    public function accept(DataProviderCondition $condition) : bool
    {
        if ($this->getConditionKey() == $condition->getKey()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public static function getConditionKey() : string
    {
        return static::$conditionKey;
    }

    /**
     * Make uri appropriate for current provider
     *
     * @param string $uri
     * @param mixed $condition
     *
     * @return string
     */
    protected function adjustUri(string $uri = '', $condition) : string
    {
        return $uri;
    }
}
