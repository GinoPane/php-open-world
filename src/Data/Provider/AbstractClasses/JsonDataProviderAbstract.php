<?php

namespace OpenWorld\Data\Provider\AbstractClasses;

use OpenWorld\Data\Provider\Interfaces\{
    DataProviderInterface,
    LoadFileInterface
};

abstract class JsonDataProviderAbstract implements DataProviderInterface, LoadFileInterface {

    public function loadFile(string $path = '')
    {

    }

}