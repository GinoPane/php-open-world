<?php

namespace OpenWorld\Data\Providers;

use OpenWorld\Data\AbstractClasses\DataProviderAbstract;
use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;

class GeneralProvider extends DataProviderAbstract
{

    public function load(string $uri = '', $condition = null) : SourceLoaderResultInterface
    {
        // TODO: Implement load() method.
        return parent::load($uri);
    }

    public function accept(string $condition = '') : bool
    {
        // TODO: Implement accept() method.
    }
}
