<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Data\GeneralClasses\SourceLoaderResults\Factories;

use GinoPane\PhpOpenWorld\Data\Interfaces\SourceLoaderResultInterface;
use GinoPane\PhpOpenWorld\Data\GeneralClasses\SourceLoaderResults\JsonResult;
use GinoPane\PhpOpenWorld\Data\Interfaces\SourceLoaderResultFactoryInterface;

/**
 * Class JsonResultFactory
 *
 * Returns new JsonSourceLoaderResults instances.
 *
 * @package GinoPane\PhpOpenWorld\Data\SourceLoaderResults\Factories
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
