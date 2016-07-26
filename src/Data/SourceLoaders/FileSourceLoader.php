<?php

namespace OpenWorld\Data\SourceLoaders;

use OpenWorld\Data\AbstractClasses\SourceLoaderAbstract;
use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;
use OpenWorld\Exceptions\FileNotFoundException;
use OpenWorld\Exceptions\FileNotValidException;

/**
 * Class FileSourceLoader
 * 
 * Provides a method for loading json data files.
 * 
 * @package OpenWorld\Data\Loaders
 */
class FileSourceLoader extends SourceLoaderAbstract {

    public function load(string $path) : SourceLoaderResultInterface
    {
        $data = $this->loadFile($path);

        $result = $this->getResultFactory()->get();

        $result->setContent($data);

        return $result;
    }

    private function loadFile(string $path)
    {
        if (!is_readable($path)) {
            throw new FileNotFoundException($path);
        }

        if (!is_file($path)) {
            throw new FileNotValidException($path);
        }

        $data = @file_get_contents($path);

        if ($data === false) {
            throw new FileNotValidException($path);
        }

        return $data;
    }

}