<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\Interfaces;

use OpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;

/**
 * Interface DataProviderInterface
 * @package OpenWorld\Data\Interfaces
 */
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
