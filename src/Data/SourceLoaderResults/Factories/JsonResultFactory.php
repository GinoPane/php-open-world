<?php

namespace OpenWorld\Data\SourceLoaderResults\Factories;

use OpenWorld\Data\SourceLoaderResults\JsonResult;
use OpenWorld\Data\Interfaces\SourceLoaderResultFactoryInterface;
use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;

/**
 * Class JsonResultFactory
 * 
 * Returns new JsonSourceLoaderResults instances.
 * 
 * @package OpenWorld\Data\SourceLoaderResults\Factories
 */
class JsonResultFactory implements SourceLoaderResultFactoryInterface {

    /**
     * Creates new JsonResult instances.
     *
     * @return SourceLoaderResultInterface
     */
    public static function get(): SourceLoaderResultInterface
    {
        return new JsonResult();
    }
    
}