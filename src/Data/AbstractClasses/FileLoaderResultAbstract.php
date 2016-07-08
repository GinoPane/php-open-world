<?php

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\FileLoaderResultInterface;
use OpenWorld\Exceptions\NotImplemented;

abstract class FileLoaderResultAbstract implements FileLoaderResultInterface {

    protected $content;

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        throw new NotImplemented(__FUNCTION__);
    }

    /**
     * @inheritDoc
     */
    public function asArray() : array
    {
        throw new NotImplemented(__FUNCTION__);
    }

    /**
     * @inheritDoc
     */
    public function asObject() : Object
    {
        throw new NotImplemented(__FUNCTION__);
    }
}