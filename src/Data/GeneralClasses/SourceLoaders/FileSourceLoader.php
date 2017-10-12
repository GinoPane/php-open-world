<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\GeneralClasses\SourceLoaders;

use OpenWorld\Exceptions\FileNotFoundException;
use OpenWorld\Exceptions\FileNotValidException;
use OpenWorld\Data\Interfaces\SourceLoaderInterface;
use OpenWorld\Exceptions\BadDataFileContentsException;

/**
 * Class FileSourceLoader
 *
 * Provides a method for loading json data files.
 *
 * @package OpenWorld\Data\Loaders
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
