<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Exceptions;

use GinoPane\PhpOpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class InvalidContentException
 *
 * An exception raised when an invalid content is being set.
 *
 * @package GinoPane\PhpOpenWorld\Exceptions
 */
class InvalidContentException extends ExceptionAbstract
{
    /**
     * Initializes the instance.
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        $message = "Invalid content is being set" . ($message ? ": $message" : $message);

        parent::__construct($message);
    }
}
