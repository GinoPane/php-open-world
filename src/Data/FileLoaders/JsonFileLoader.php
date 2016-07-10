<?php

namespace OpenWorld\Data\Loaders;

use OpenWorld\Data\AbstractClasses\FileLoaderAbstract;
use OpenWorld\Data\Interfaces\FileLoaderInterface;
use OpenWorld\Data\Interfaces\FileLoaderResultInterface;

/**
 * Class JsonFileLoader
 * 
 * Provides a method for loading json data files.
 * 
 * @package OpenWorld\Data\Loaders
 */
class JsonFileLoader extends FileLoaderAbstract {

    public function load(string $path = '') : FileLoaderResultInterface
    {
        if (!(is_string($identifier) && isset($identifier[0]))) {
            throw new Exception\InvalidDataFile($identifier);
        }
        if (empty($locale)) {
            $locale = static::$defaultLocale;
        }
        if (!isset(static::$cache[$locale])) {
            static::$cache[$locale] = array();
        }
        if (!isset(static::$cache[$locale][$identifier])) {
            if (!@preg_match('/^[a-zA-Z0-9_\\-]+$/', $identifier)) {
                throw new Exception\InvalidDataFile($identifier);
            }
            $dir = static::getLocaleFolder($locale);
            if (!isset($dir[0])) {
                throw new Exception\DataFolderNotFound($locale, static::$fallbackLocale);
            }
            $file = $dir.DIRECTORY_SEPARATOR.$identifier.'.json';
            if (!is_file(__DIR__.DIRECTORY_SEPARATOR.$file)) {
                throw new Exception\DataFileNotFound($identifier, $locale, static::$fallbackLocale);
            }
            $json = @file_get_contents(__DIR__.DIRECTORY_SEPARATOR.$file);
            //@codeCoverageIgnoreStart
            // In test enviro we can't replicate this problem
            if ($json === false) {
                throw new Exception\DataFileNotReadable($file);
            }
            //@codeCoverageIgnoreEnd
            $data = @json_decode($json, true);
            //@codeCoverageIgnoreStart
            // In test enviro we can't replicate this problem
            if (!is_array($data)) {
                throw new Exception\BadDataFileContents($file, $json);
            }
            //@codeCoverageIgnoreEnd
            static::$cache[$locale][$identifier] = $data;
        }

        return static::$cache[$locale][$identifier];
    }

}