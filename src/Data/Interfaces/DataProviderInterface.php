<?php

namespace OpenWorld\Data\Interfaces;

interface DataProviderInterface {

    /**
     * @param string $uri Path to the resource to load
     *
     * @return SourceLoaderResultInterface
     */
    public function load(string $uri = '', $condition = null) : SourceLoaderResultInterface;

    /**
     * @param string $condition Checks if provider accepts the condition
     *
     * @return bool
     */
    public function accept(string $condition = '') : bool;

}