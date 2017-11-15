<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Exceptions;

use GinoPane\PhpOpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class InvalidAssertionKeyException
 *
 * An exception raised when an invalid key for key assertion was used
 *
 * @package GinoPane\PhpOpenWorld\Exceptions
 */
class InvalidAssertionKeyException extends ExceptionAbstract
{
    /**
     * An invalid key
     *
     * @var string
     */
    protected $key;

    /**
     * Initializes the instance.
     *
     * @param string $key The bad key
     * @param string $className Class with an issue
     */
    public function __construct(string $key, string $className)
    {
        $this->key = $key;

        $message = "Key property '$key' is not a present in the class '$className'";

        parent::__construct($message);
    }

    /**
     * Retrieves the bad key
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
