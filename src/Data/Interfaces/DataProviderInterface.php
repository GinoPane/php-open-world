<?php

namespace OpenWorld\Data\Interfaces;

interface DataProviderInterface {

    public function load(string $uri = '') : SourceLoaderResultInterface;

    public function accept(string $condition = '') : bool;

}