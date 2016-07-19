<?php

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\SourceLoaderInterface;
use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;
use OpenWorld\Data\Interfaces\SourceLoaderResultFactoryInterface;

abstract class SourceLoaderAbstract implements SourceLoaderInterface {

    /**
     * Represents result of source loading.
     *
     * @var SourceLoaderResultInterface
     */
    protected $resultClass = '';

    public function __construct(SourceLoaderResultFactoryInterface $resultClass)
    {
        $this->setResultFactory($resultClass);
    }

    public function setResultFactory(SourceLoaderResultFactoryInterface $resultClass)
    {
        $this->resultClass = $resultClass;
    }

    public function getResultFactory() : SourceLoaderResultFactoryInterface
    {
        return $this->resultClass;
    }

}