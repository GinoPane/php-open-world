<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Data\Interfaces;

/**
 * Interface SourceLoaderInterface
 * @package GinoPane\PhpOpenWorld\Data\Interfaces
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
