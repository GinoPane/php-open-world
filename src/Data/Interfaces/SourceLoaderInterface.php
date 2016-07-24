<?php

namespace OpenWorld\Data\Interfaces;

interface SourceLoaderInterface {

    public function load(string $path) : SourceLoaderResultInterface;

}