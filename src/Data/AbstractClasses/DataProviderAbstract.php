<?php

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\{
    DataProviderInterface, SourceLoaderInterface, SourceLoaderResultFactoryInterface, SourceLoaderResultInterface
};

/**
 * Class ProviderAbstract
 *
 * Starting point for data provider classes.
 *
 * @package OpenWorld\Data\AbstractClasses
 */
abstract class DataProviderAbstract implements DataProviderInterface {

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
    protected $resultClass = '';

    /**
     * ProviderAbstract constructor.
     *
     * @param SourceLoaderInterface $loader
     * @param SourceLoaderResultFactoryInterface $resultClass
     *
     * return void
     */
    public function __construct(SourceLoaderInterface $loader, SourceLoaderResultFactoryInterface $resultClass)
    {
        $this->setLoader($loader);
        $this->setResultFactory($resultClass);
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
     * @param SourceLoaderResultFactoryInterface $resultClass
     */
    public function setResultFactory(SourceLoaderResultFactoryInterface $resultClass)
    {
        $this->resultClass = $resultClass;
    }

    /**
     * @return SourceLoaderResultFactoryInterface
     */
    public function getResultFactory() : SourceLoaderResultFactoryInterface
    {
        return $this->resultClass;
    }

    public function load(string $uri = '', $condition = null) : SourceLoaderResultInterface
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
     * Make uri appropriate for current provider
     *
     * @param string $uri
     * @param null $condition
     *
     * @return string
     */
    protected function adjustUri(string $uri = '', $condition = null) : string
    {
        return $uri;
    }
}
