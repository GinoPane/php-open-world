<?php

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class InvalidDataFile
 *
 * An exception raised when an invalid data file has been hit.
 *
 * @package OpenWorld\Exceptions
 */
class InvalidDataFile extends ExceptionAbstract
{
    protected $identifier;

    /**
     * Initializes the instance.
     *
     * @param mixed $identifier The bad data file identifier
     */
    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;

        $message = "'$identifier' is not a valid data file identifier";

        parent::__construct($message);
    }

    /**
     * Retrieves the bad data file identifier.
     *
     * @return mixed
     */
    public function getIdentifier() : string
    {
        return $this->identifier;
    }
}
