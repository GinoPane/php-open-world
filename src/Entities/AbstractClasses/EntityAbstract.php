<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities\AbstractClasses;

use OpenWorld\Data\GeneralClasses\OpenWorldDataSource;

/**
 * Class EntityAbstract
 *
 * @package OpenWorld\Entities\AbstractClasses
 */
abstract class EntityAbstract
{
    /**
     * Source URI for alias substitution data
     *
     * @var string
     */
    protected static $aliasSourceUri = '';

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
     * Checks if the code is actually an alias for a real code
     *
     * @param string $code Initial code, that need to be checked
     *
     * @return string
     */
    protected static function getCodeFromAlias(string $code): string
    {
        if (static::$aliasSourceUri) {
            $aliasData = self::getDataSourceLoader()->loadGeneral(static::$aliasSourceUri);

            if (!empty($aliasData[$code])) {
                $replacementData = $aliasData[$code];

                $replacement = !empty($replacementData['replacement'])
                    ? explode(" ", $replacementData['replacement'])
                    : [];

                if ($replacement) {
                    $replacementCode = reset($replacement);

                    $code = $replacementCode;
                }
            }
        }

        return $code;
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