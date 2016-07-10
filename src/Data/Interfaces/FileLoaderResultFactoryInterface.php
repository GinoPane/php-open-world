<?php

namespace OpenWorld\Data\Interfaces;

/**
 * Interface FileLoaderResultFactoryInterface
 *
 * Returns new FileLoaderResultInterface instances.
 *
 * @package OpenWorld\Data\Interfaces
 */
interface FileLoaderResultFactoryInterface {

    /**
     * Get new FileLoaderResultInterface instance.
     *
     * @return FileLoaderResultInterface
     */
    public static function get(): FileLoaderResultInterface;

}