<?php

namespace OpenWorld\Data\Interfaces;

use stdClass;

interface SourceLoaderResultInterface {

    /**
     * Get result data as string
     *
     * @return string
     */
    public function asString(): string;

    /**
     * Get result data as array
     *
     * @return array
     */
    public function asArray() : array;

    /**
     * Get result data as object
     *
     * @return stdClass
     */
    public function asObject() : stdClass;

    /**
     * Set result content.
     *
     * @param $content
     * @return mixed
     *
     * @throws InvalidContentException
     */
    public function setContent($content);

    /**
     * Get result's content.
     *
     * @return mixed
     */
    public function getContent();

    /**
     * Checks whether content is valid for the result.
     *
     * @param $content
     * @return bool
     */
    public function isValid($content) : bool;

}