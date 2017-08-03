<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities\Traits;

use Exception;
use OpenWorld\Data\GeneralClasses\OpenWorldDataSource;

/**
 * Class ImplementsAliasSubstitution
 *
 * @package OpenWorld\Entities\Traits
 */
trait ImplementsAliasSubstitution
{
    /**
     * Source URI for alias substitution data
     *
     * @var string
     */
    protected $aliasSourceUri = '';

    /**
     * Checks if the code is actually an alias for a real code
     *
     * @param string $code Initial code, that need to be checked
     *
     * @param OpenWorldDataSource $dataSource
     *
     * @return string
     */
    protected function getCodeFromAlias(string $code, OpenWorldDataSource $dataSource): string
    {
        $aliasData = $dataSource->loadGeneral($this->aliasSourceUri)->asArray();

        if (!empty($aliasData[$code])) {
            $replacementData = $aliasData[$code];

            $replacement = !empty($replacementData['replacement']) ? explode(" ", $replacementData['replacement']) : [];

            if ($replacement) {
                $replacementCode = reset($replacement);

                $code = $replacementCode;
            }
        }

        return $code;
    }
}