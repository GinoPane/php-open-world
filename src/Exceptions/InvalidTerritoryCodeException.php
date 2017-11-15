<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Exceptions;

use GinoPane\PhpOpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class InvalidTerritoryCodeException
 *
 * An exception raised when an invalid territory code is passed
 *
 * @package GinoPane\PhpOpenWorld\Exceptions
 */
class InvalidTerritoryCodeException extends ExceptionAbstract
{
    /**
     * An invalid code
     *
     * @var string
     */
    protected $code;

    /**
     * Code type used
     *
     * @var string
     */
    protected $codeType;

    /**
     * Initializes the instance
     *
     * @param string $code The invalid code type
     * @param string $codeType The code type being used
     */
    public function __construct(string $code, string $codeType = '')
    {
        $this->code = $code;

        $message = "'$code' is not a valid code" . ($codeType ? "for code type '$codeType'" : " for Territory instance");

        parent::__construct($message);
    }

    /**
     * Retrieves the bad code
     *
     * @return string
     */
    public function getTerritoryCode(): string
    {
        return $this->code;
    }

    /**
     * Retrieves the code type
     *
     * @return string
     */
    public function getTerritoryCodeType(): string
    {
        return $this->codeType;
    }
}
