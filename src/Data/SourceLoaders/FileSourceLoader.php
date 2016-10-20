<?php

namespace OpenWorld\Data\SourceLoaders;

use OpenWorld\Data\Interfaces\SourceLoaderInterface;
use OpenWorld\Exceptions\BadDataFileContentsException;
use OpenWorld\Exceptions\FileNotFoundException;
use OpenWorld\Exceptions\FileNotValidException;

/**
 * Class FileSourceLoader
 * 
 * Provides a method for loading json data files.
 * 
 * @package OpenWorld\Data\Loaders
 */
class FileSourceLoader implements SourceLoaderInterface {

    public function loadSource(string $path) : string
    {
        if (!is_readable($path)) {
            throw new FileNotFoundException($path);
        }

        if (!is_file($path) || is_dir($path)) {
            throw new FileNotValidException($path);
        }

        $data = @file_get_contents($path);

        if ($data === false) {
            throw new BadDataFileContentsException($path, $data);
        }

        return $data;
    }
}