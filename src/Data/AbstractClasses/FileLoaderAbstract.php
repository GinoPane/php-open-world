<?php

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\FileLoaderInterface;
use OpenWorld\Data\Interfaces\FileLoaderResultInterface;

abstract class FileLoaderAbstract implements FileLoaderInterface {

    protected $resultClass = '';

    abstract public function __construct(FileLoaderResultInterface $resultClass = null);

    public function setResultClass(string $className)
    {

    }

    public function getResultClass() : string
    {

    }

}