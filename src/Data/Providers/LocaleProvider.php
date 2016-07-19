<?php

namespace OpenWorld\Data\Providers;

use OpenWorld\Data\AbstractClasses\DataProviderAbstract;
use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;

class LocaleProvider extends DataProviderAbstract {

    public function load(string $uri = '') : SourceLoaderResultInterface
    {
        // TODO: Implement load() method.

        $this->getLoader()->load($uri);
    }

    public function accept(string $condition = '') : bool
    {
        // TODO: Implement accept() method.
    }

}
