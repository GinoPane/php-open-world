<?php

namespace OpenWorld\Data\Interfaces;

interface DataProviderInterface {

    public function load(string $fileName = '') : FileLoaderResultInterface;

    public function accept(string $condition = '') : bool;

}