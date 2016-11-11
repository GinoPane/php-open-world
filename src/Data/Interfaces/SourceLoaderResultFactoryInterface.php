<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\Interfaces;

/**
 * Interface SourceLoaderResultFactoryInterface
 *
 * Returns new SourceLoaderResultInterface instances.
 *
 * @package OpenWorld\Data\Interfaces
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
