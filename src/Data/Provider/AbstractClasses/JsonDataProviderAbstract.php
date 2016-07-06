<?php

namespace OpenWorld\Data\Provider\AbstractClasses;

use OpenWorld\Data\Provider\Interfaces\{
    DataProviderInterface,
    LoadFileInterface
};

/**
 * Class JsonDataProviderAbstract
 * 
 * Provides a method for loading json data files.
 * 
 * @package OpenWorld\Data\Provider\AbstractClasses
 */
abstract class JsonDataProviderAbstract implements DataProviderInterface, LoadFileInterface {

    public function loadFile(string $path = '')
    {

    }

}