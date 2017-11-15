<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Data\Interfaces;

use GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;

/**
 * Interface DataProviderInterface
 * @package GinoPane\PhpOpenWorld\Data\Interfaces
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
    public function accept(DataProviderCondition $condition): bool;
}
