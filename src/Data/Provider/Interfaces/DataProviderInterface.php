<?php

namespace OpenWorld\Data\Provider\Interfaces;

interface DataProviderInterface {

    public function load(string $fileName = '');

    public function accept(string $condition = '');

}