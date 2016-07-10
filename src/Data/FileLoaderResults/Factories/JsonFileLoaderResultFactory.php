<?php

namespace OpenWorld\Data\FileLoaderResults\Factories;

use OpenWorld\Data\FileLoaderResults\JsonFileLoaderResult;
use OpenWorld\Data\Interfaces\FileLoaderResultFactoryInterface;
use OpenWorld\Data\Interfaces\FileLoaderResultInterface;

/**
 * Class JsonFileLoaderResultFactory
 * 
 * Returns new JsonFileLoaderResults instances.
 * 
 * @package OpenWorld\Data\FileLoaderResults\Factories
 */
class JsonFileLoaderResultFactory implements FileLoaderResultFactoryInterface {

    /**
     * Creates new JsonFileLoaderResult instances.
     *
     * @return FileLoaderResultInterface
     */
    public static function get(): FileLoaderResultInterface
    {
        return new JsonFileLoaderResult();
    }
    
}