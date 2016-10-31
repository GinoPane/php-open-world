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
     * @param string $template Template for the message. %1 placeholder for actual type, %2 - for allowed
     */
    public function __construct(string $actualType, string $allowedType, string $template = '')
    {
        $this->actual = $actualType;
        $this->allowed = $allowedType;

        $message = "'%1' is not allowed. Allowed type is: '%2'";

        if ($template) {
            $message = $template;
        }

        $message = $this->fillTemplate($message, $actualType, $allowedType);


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

    /**
     * Possible placeholders:
     *  %1 - for actual type
     *  %2 - for allowed type
     *
     * @param string $template
     * @param string $actualType
     * @param string $allowedType
     * @return string
     */
    private function fillTemplate(string $template, string $actualType, string $allowedType) : string
    {
        $message = '';

        $message = str_replace('%1', $actualType, $template);
        $message = str_replace('%2', $allowedType, $message);

        return $message;
    }
}
