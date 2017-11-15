<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Data\Interfaces;

use GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;

/**
 * Interface DataSourceInterface
 *
 * @package GinoPane\PhpOpenWorld\Data\Interfaces
 */
interface DataSourceInterface
{

    /**
     * Loads data specified by URI using providers selected by conditions
     *
     * @param string $uri Path to the resource to load
     * @param DataProviderCondition $condition Conditions that should be accepted while loading data
     *
     * @return SourceLoaderResultInterface
     */
    public function load(string $uri, DataProviderCondition $condition): SourceLoaderResultInterface;
}
