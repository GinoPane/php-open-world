<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Exceptions;

use GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers\Conditions\DataProviderCondition;
use GinoPane\PhpOpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class DataNotAvailableException
 *
 * An exception raised when a necessary data was not loaded.
 *
 * @package GinoPane\PhpOpenWorld\Exceptions
 */
class DataNotAvailableException extends ExceptionAbstract
{
    /**
     * URI of the necessary data
     *
     * @var string
     */
    protected $uri;

    /**
     * Conditions for a data provider
     *
     * @var
     */
    protected $condition;

    /**
     * Initializes the instance.
     *
     * @param string $uri URI of the necessary data
     * @param DataProviderCondition $condition Conditions for a data provider
     */
    public function __construct(string $uri, DataProviderCondition $condition)
    {
       $message = "Unable to get the data for uri:'$uri' with conditions:'" . (string)$condition . "'";

        parent::__construct($message);
    }

    /**
     * Returns saved URI
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Returns saved conditions
     *
     * @return mixed
     */
    public function getCondition()
    {
        return $this->condition;
    }
}
