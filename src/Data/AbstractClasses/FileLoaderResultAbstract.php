<?php

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\FileLoaderResultInterface;
use OpenWorld\Exceptions\InvalidContentException;
use OpenWorld\Exceptions\NotImplementedException;

abstract class FileLoaderResultAbstract implements FileLoaderResultInterface {

    protected $content;

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        throw new NotImplementedException(__FUNCTION__);
    }

    /**
     * @inheritDoc
     */
    public function asArray() : array
    {
        throw new NotImplementedException(__FUNCTION__);
    }

    /**
     * @inheritDoc
     */
    public function asObject() : Object
    {
        throw new NotImplementedException(__FUNCTION__);
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        $this->assert($content);

        $this->content = $content;
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function isValid($content) : bool
    {
        return true;
    }

    /**
     * Makes sure that $content is valid for this FileLoaderResultAbstract instance
     *
     * @param $content
     *
     * @throws InvalidContentException
     */
    protected function assert($content)
    {
        if (!$this->isValid($content)) {
            throw new InvalidContentException();
        }
    }
}