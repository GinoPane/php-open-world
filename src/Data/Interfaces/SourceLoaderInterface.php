<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\Interfaces;

/**
 * Interface SourceLoaderInterface
 * @package OpenWorld\Data\Interfaces
 */
interface SourceLoaderInterface
{

    /**
     * Load source's content as string
     *
     * @param string $uri
     * @return string
     */
    public function loadSource(string $uri): string;
}
