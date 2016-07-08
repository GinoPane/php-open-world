<?php

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\{
    DataProviderInterface,
    FileLoaderInterface
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
     * File loader instance.
     *
     * @var FileLoaderInterface
     */
    protected $loader = null;

    /**
     * ProviderAbstract constructor.
     *
     * @param FileLoaderInterface $loader
     *
     * return void
     */
    public function __construct(FileLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Set provider's file loader.
     *
     * @param FileLoaderInterface $loader
     *
     * return void
     */
    public function setLoader(FileLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Get provider's file loader.
     *
     * @return FileLoaderInterface
     */
    public function getLoader() : FileLoaderInterface
    {
        return $this->loader;
    }

}
