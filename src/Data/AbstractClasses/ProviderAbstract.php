<?php

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\{
    DataProviderInterface,
    LoadFileInterface
};

abstract class ProviderAbstract implements DataProviderInterface {

    /**
     * File loader instance.
     *
     * @var LoadFileInterface
     */
    protected $loader = null;

    public function __construct(LoadFileInterface $loader)
    {
        $this->loader = $loader;
    }

}
