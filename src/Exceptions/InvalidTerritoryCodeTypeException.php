<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class InvalidTerritoryCodeTypeException
 *
 * An exception raised when an invalid territory code type is passed
 *
 * @package OpenWorld\Exceptions
 */
class InvalidTerritoryCodeTypeException extends ExceptionAbstract
{
    /**
     * An invalid code type
     *
     * @var string
     */
    protected $codeType;

    /**
     * Initializes the instance
     *
     * @param string $codeType The invalid code type
     */
    public function __construct(string $codeType)
    {
        $this->codeType = $codeType;

        $message = "'$codeType' is not a valid code type for Territory instance";

        parent::__construct($message);
    }

    /**
     * Retrieves the bad code type
     *
     * @return string
     */
    public function getCodeType(): string
    {
        return $this->codeType;
    }
}
