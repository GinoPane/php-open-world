<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Data\GeneralClasses\SourceLoaders;

use GinoPane\PhpOpenWorld\Exceptions\FileNotFoundException;
use GinoPane\PhpOpenWorld\Exceptions\FileNotValidException;
use GinoPane\PhpOpenWorld\Data\Interfaces\SourceLoaderInterface;
use GinoPane\PhpOpenWorld\Exceptions\BadDataFileContentsException;

/**
 * Class FileSourceLoader
 *
 * Provides a method for loading json data files.
 *
 * @package GinoPane\PhpOpenWorld\Data\Loaders
 */
class FileSourceLoader implements SourceLoaderInterface
{
    /**
     * Load source's content as string
     *
     * @param string $uri
     * @return string
     * @throws BadDataFileContentsException
     * @throws FileNotFoundException
     * @throws FileNotValidException
     */
    public function loadSource(string $uri): string
    {
        if (!is_readable($uri)) {
            throw new FileNotFoundException($uri);
        }

        if (!is_file($uri) || is_dir($uri)) {
            throw new FileNotValidException($uri);
        }

        $data = @file_get_contents($uri);

        if ($data === false) {
            throw new BadDataFileContentsException($uri, $data);
        }

        return $data;
    }
}
