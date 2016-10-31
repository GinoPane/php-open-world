<?php

namespace OpenWorld\Data\Interfaces;

interface DataSourceInterface
{

    public function load($uri = '', $condition = '');
}
