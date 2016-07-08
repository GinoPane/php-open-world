<?php

namespace OpenWorld\Data\Interfaces;

interface FileLoaderResultInterface {

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
     * @return Object
     */
    public function asObject() : Object;

}