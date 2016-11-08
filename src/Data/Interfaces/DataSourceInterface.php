<?php

namespace OpenWorld\Data\Interfaces;

use OpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;

interface DataSourceInterface
{
    /**
     * @param string $uri Path to the resource to load
     * @param DataProviderCondition $condition Conditions that should be accepted while loading data
     *
     * @return SourceLoaderResultInterface
     */
    public function load(string $uri = '', DataProviderCondition $condition) : SourceLoaderResultInterface;
}
