<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities\AbstractClasses;

use OpenWorld\OpenWorld;
use OpenWorld\Data\GeneralClasses\OpenWorldDataSource;

/**
 * Class EntityAbstract
 *
 * @package OpenWorld\Entities\AbstractClasses
 */
abstract class EntityAbstract
{
    /**
     * Returns entity code. It could be territory code for Territory, script code for Script
     *
     * @return string
     */
    abstract public function getCode(): string;

    /**
     * Returns a string representation of an instance
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getCode();
    }

    /**
     * Returns OpenWorldDataSource instance to load source data
     *
     * @return OpenWorldDataSource
     */
    protected static function getDataSourceLoader(): OpenWorldDataSource
    {
        return OpenWorldDataSource::getInstance();
    }
}