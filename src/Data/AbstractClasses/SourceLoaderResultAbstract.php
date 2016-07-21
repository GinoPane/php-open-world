<?php

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;
use OpenWorld\Exceptions\InvalidContentException;
use OpenWorld\Exceptions\NotImplementedException;
use stdClass;

abstract class SourceLoaderResultAbstract implements SourceLoaderResultInterface {

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
    public function asObject() : stdClass
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
     * Makes sure that $content is valid for this SourceLoaderResultAbstract instance
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