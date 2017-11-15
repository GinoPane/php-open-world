<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Data\Interfaces;

/**
 * Interface SourceLoaderResultFactoryInterface
 *
 * Returns new SourceLoaderResultInterface instances.
 *
 * @package GinoPane\PhpOpenWorld\Data\Interfaces
 */
interface SourceLoaderResultFactoryInterface
{

    /**
     * Get new SourceLoaderResultInterface instance.
     *
     * @return SourceLoaderResultInterface
     */
    public static function get(): SourceLoaderResultInterface;
}
