<?php

namespace OpenWorld\Data\Interfaces;

interface SourceLoaderInterface
{

    public function loadSource(string $path) : string;
}
