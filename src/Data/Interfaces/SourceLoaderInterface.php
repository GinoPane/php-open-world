<?php

namespace OpenWorld\Data\Interfaces;

interface SourceLoaderInterface
{
    /**
     * Load source's content as string
     *
     * @param string $path
     * @return string
     */
    public function loadSource(string $path) : string;
}
