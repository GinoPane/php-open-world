<?php

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\FileLoaderInterface;
use OpenWorld\Data\Interfaces\FileLoaderResultInterface;
use OpenWorld\Data\Interfaces\FileLoaderResultFactoryInterface;

abstract class FileLoaderAbstract implements FileLoaderInterface {

    /**
     * Represents result of file loading.
     *
     * @var FileLoaderResultInterface
     */
    protected $resultClass = '';

    public function __construct(FileLoaderResultFactoryInterface $resultClass)
    {
        $this->setResultFactory($resultClass);
    }

    public function setResultFactory(FileLoaderResultFactoryInterface $resultClass)
    {
        $this->resultClass = $resultClass;
    }

    public function getResultFactory() : FileLoaderResultFactoryInterface
    {
        return $this->resultClass;
    }

}