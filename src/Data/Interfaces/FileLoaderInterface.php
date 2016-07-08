<?php

namespace OpenWorld\Data\Interfaces;

interface FileLoaderInterface {

    public function load(string $path = '') : FileLoaderResultInterface;

}