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
 * Class NoDataProvidersAvailableException
 *
 * An exception raised when no providers were found for a source
 *
 * @package GinoPane\PhpOpenWorld\Exceptions
 */
class NoDataProvidersAvailableException extends ExceptionAbstract
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
     * Initializes the instance
     *
     * @param string $uri URI of the necessary data
     * @param DataProviderCondition $condition Conditions for a data provider
     */
    public function __construct(string $uri, DataProviderCondition $condition)
    {
       $message = "Unable to find providers for uri:'$uri' with conditions:'" . (string)$condition . "'";

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
