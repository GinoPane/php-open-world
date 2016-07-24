<?php

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\SourceLoaderInterface;
use OpenWorld\Data\Interfaces\SourceLoaderResultFactoryInterface;

abstract class SourceLoaderAbstract implements SourceLoaderInterface {

    /**
     * Represents result of source loading.
     *
     * @var SourceLoaderResultFactoryInterface
     */
    protected $resultClass = '';

    /**
     * SourceLoaderAbstract constructor.
     *
     * @param SourceLoaderResultFactoryInterface $resultClass
     */
    public function __construct(SourceLoaderResultFactoryInterface $resultClass)
    {
        $this->setResultFactory($resultClass);
    }

    /**
     * @param SourceLoaderResultFactoryInterface $resultClass
     */
    public function setResultFactory(SourceLoaderResultFactoryInterface $resultClass)
    {
        $this->resultClass = $resultClass;
    }

    /**
     * @return SourceLoaderResultFactoryInterface
     */
    public function getResultFactory() : SourceLoaderResultFactoryInterface
    {
        return $this->resultClass;
    }

}