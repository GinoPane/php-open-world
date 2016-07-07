<?php

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class ValueNotInList
 *
 * An exception raised when a non-listed value is used.
 *
 * @package OpenWorld\Exceptions
 */
class ValueNotInList extends ExceptionAbstract
{
    protected $value;

    protected $allowedValues;
    
    /**
     * Initializes the instance.
     *
     * @param string $value The invalid value
     * @param array<string|numeric> $allowedValues The list of valid values
     */
    public function __construct(string $value, array $allowedValues)
    {
        $this->value = $value;
        $this->allowedValues = $allowedValues;

        $message = "'$value' is not valid. Acceptable values are: '".implode("', '", $allowedValues)."'";

        parent::__construct($message);
    }

    /**
     * Retrieves the invalid value.
     *
     * @return string
     */
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * Retrieves the list of valid values.
     *
     * @return array
     */
    public function getAllowedValues() : array
    {
        return $this->allowedValues;
    }
}
