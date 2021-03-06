<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class InvalidLocaleException
 *
 * An exception raised when an invalid locale specification has been hit.
 *
 * @package OpenWorld\Exceptions
 */
class InvalidLocaleException extends ExceptionAbstract
{
    /**
     * An invalid locale
     *
     * @var string
     */
    protected $locale;

    /**
     * Initializes the instance.
     *
     * @param mixed $locale The bad locale
     */
    public function __construct(string $locale)
    {
        $this->locale = $locale;

        $message = "'$locale' is not a valid locale identifier";

        parent::__construct($message);
    }

    /**
     * Retrieves the bad locale.
     *
     * @return mixed
     */
    public function getLocale() : string
    {
        return $this->locale;
    }
}
