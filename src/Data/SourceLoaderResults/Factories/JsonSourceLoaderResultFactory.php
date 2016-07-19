<?php

namespace OpenWorld\Data\SourceLoaderResults\Factories;

use OpenWorld\Data\SourceLoaderResults\JsonSourceLoaderResult;
use OpenWorld\Data\Interfaces\SourceLoaderResultFactoryInterface;
use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;

/**
 * Class JsonSourceLoaderResultFactory
 * 
 * Returns new JsonSourceLoaderResults instances.
 * 
 * @package OpenWorld\Data\SourceLoaderResults\Factories
 */
class JsonSourceLoaderResultFactory implements SourceLoaderResultFactoryInterface {

    /**
     * Creates new JsonSourceLoaderResult instances.
     *
     * @return SourceLoaderResultInterface
     */
    public static function get(): SourceLoaderResultInterface
    {
        return new JsonSourceLoaderResult();
    }
    
}