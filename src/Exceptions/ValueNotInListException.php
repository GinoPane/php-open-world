<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class ValueNotInListException
 *
 * An exception raised when a non-listed value is used.
 *
 * @package OpenWorld\Exceptions
 */
class ValueNotInListException extends ExceptionAbstract
{
    /**
     * An invalid value
     *
     * @var string
     */
    protected $value;

    /**
     * Array of allowed values
     *
     * @var array
     */
    protected $allowedValues;

    /**
     * Initializes the instance.
     *
     * @param string $value The invalid value
     * @param array <string|numeric> $allowedValues The list of valid values
     */
    public function __construct(string $value, array $allowedValues)
    {
        $this->value = $value;
        $this->allowedValues = $allowedValues;

        $message = "'$value' is not valid. Acceptable values are: '" . implode("', '", $allowedValues) . "'";

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
