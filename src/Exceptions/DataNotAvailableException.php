<?php

namespace OpenWorld\Exceptions;

use OpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;
use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class DataNotAvailableException
 *
 * An exception raised when a necessary data was not loaded.
 *
 * @package OpenWorld\Exceptions
 */
class DataNotAvailableException extends ExceptionAbstract
{
    /**
     * @var string
     */
    protected $uri;

    /**
     * @var
     */
    protected $condition;

    /**
     * Initializes the instance.
     *
     * @param string $uri
     * @param DataProviderCondition $condition
     */
    public function __construct(string $uri, DataProviderCondition $condition)
    {
       $message = "Unable to get the data for uri:'$uri' with conditions:'" . (string)$condition . "'";

        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return mixed
     */
    public function getCondition()
    {
        return $this->condition;
    }
}
