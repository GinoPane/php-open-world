<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\Interfaces;

use OpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;

/**
 * Interface DataSourceInterface
 *
 * @package OpenWorld\Data\Interfaces
 */
interface DataSourceInterface
{

    /**
     * Loads data specified by URI using providers selected by conditions.
     *
     * @param string $uri Path to the resource to load
     * @param DataProviderCondition $condition Conditions that should be accepted while loading data
     *
     * @return SourceLoaderResultInterface
     */
    public function load(string $uri = '', DataProviderCondition $condition) : SourceLoaderResultInterface;
}
