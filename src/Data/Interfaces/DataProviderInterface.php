<?php

namespace OpenWorld\Data\Interfaces;

use OpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;

interface DataProviderInterface extends DataSourceInterface
{
    /**
     * Checks if provider accepts the condition
     *
     * @param mixed $condition
     *
     * @return bool
     */
    public function accept(DataProviderCondition $condition) : bool;
}
