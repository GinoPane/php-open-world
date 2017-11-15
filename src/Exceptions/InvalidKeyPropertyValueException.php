<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Exceptions;

use GinoPane\PhpOpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class InvalidKeyPropertyValueException
 *
 * An exception raised when an invalid key for key assertion was used
 *
 * @package GinoPane\PhpOpenWorld\Exceptions
 */
class InvalidKeyPropertyValueException extends ExceptionAbstract
{
    /**
     * Invalid value
     *
     * @var string
     */
    protected $value;

    /**
     * Initializes the instance.
     *
     * @param string $value Invalid value
     * @param string $className Class with an issue
     */
    public function __construct(string $value, string $className)
    {
        $this->value = $value;

        $message = "Key property value ('$value') is not valid for the class '$className'";

        parent::__construct($message);
    }

    /**
     * Retrieves the bad value
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->value;
    }
}
