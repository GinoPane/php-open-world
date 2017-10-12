<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\GeneralClasses\SourceLoaderResults\Factories;

use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;
use OpenWorld\Data\GeneralClasses\SourceLoaderResults\JsonResult;
use OpenWorld\Data\Interfaces\SourceLoaderResultFactoryInterface;

/**
 * Class JsonResultFactory
 *
 * Returns new JsonSourceLoaderResults instances.
 *
 * @package OpenWorld\Data\SourceLoaderResults\Factories
 */
class JsonResultFactory implements SourceLoaderResultFactoryInterface
{

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
