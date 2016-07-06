<?php

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class NotImplemented
 *
 * An exception raised when a not implemented function is called.
 *
 * @package OpenWorld\Exceptions
 */
class NotImplemented extends ExceptionAbstract
{
    protected $function;

    /**
     * Initializes the instance.
     *
     * @param string $function The function that's not implemented
     */
    public function __construct(string $function)
    {
        $this->function = $function;
        $message = "$function is not implemented";

        parent::__construct($message);
    }

    /**
     * Retrieves the name of the not implemented function.
     *
     * @return string
     */
    public function getFunction() : string
    {
        return $this->function;
    }
}
