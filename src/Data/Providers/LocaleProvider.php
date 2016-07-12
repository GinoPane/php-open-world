<?php

namespace OpenWorld\Data\Providers;

use OpenWorld\Data\AbstractClasses\DataProviderAbstract;
use OpenWorld\Data\Interfaces\FileLoaderResultInterface;

class LocaleProvider extends DataProviderAbstract {

    public function load(string $fileName = '') : FileLoaderResultInterface
    {
        // TODO: Implement load() method.

        $this->getLoader()->load($fileName);
    }

    public function accept(string $condition = '') : bool
    {
        // TODO: Implement accept() method.
    }

}
