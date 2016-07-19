<?php

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\{
    DataProviderInterface,
    SourceLoaderInterface
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
     * ProviderAbstract constructor.
     *
     * @param SourceLoaderInterface $loader
     *
     * return void
     */
    public function __construct(SourceLoaderInterface $loader)
    {
        $this->loader = $loader;
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

}
