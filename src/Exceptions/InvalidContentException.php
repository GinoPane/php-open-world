<?php

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class InvalidContentException
 *
 * An exception raised when an invalid content is being set.
 *
 * @package OpenWorld\Exceptions
 */
class InvalidContentException extends ExceptionAbstract
{
    /**
     * Initializes the instance.
     */
    public function __construct()
    {
        $message = "Invalid content is being set";

        parent::__construct($message);
    }
}
