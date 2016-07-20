<?php

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class InvalidTypeException
 *
 * An exception raised when wrong-type parameter was passed.
 *
 * @package OpenWorld\Exceptions
 */
class InvalidTypeException extends ExceptionAbstract
{
    /**
     * @var string
     */
    protected $actualType;

    /**
     * @var string
     */
    protected $allowedType;
    
    /**
     * Initializes the instance.
     *
     * @param string $actualType The actual type
     * @param string $allowedType The allowed type
     */
    public function __construct(string $actualType, string $allowedType)
    {
        $this->actualType = $actualType;
        $this->allowedType = $allowedType;

        $message = "'$actualType' is not allowed. Allowed type is: '$allowedType'";

        parent::__construct($message);
    }

    /**
     * Retrieves the actual type value.
     *
     * @return string
     */
    public function getActualType() : string
    {
        return $this->actualType;
    }

    /**
     * Retrieves the valid type string value.
     *
     * @return string
     */
    public function getAllowedType() : string
    {
        return $this->allowedType;
    }
}
