<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class DataFileNotFoundException
 *
 * An exception raised when a data file has not been found.
 *
 * @package OpenWorld\Exceptions
 */
class DataFileNotFoundException extends ExceptionAbstract
{
    /**
     * The data file identifier
     *
     * @var string
     */
    protected $identifier;

    /**
     * The preferred locale (if the data file is locale-specific)
     *
     * @var string
     */
    protected $locale;

    /**
     * The fallback locale (if the data file is locale-specific)
     *
     * @var string
     */
    protected $fallbackLocale;

    /**
     * Initializes the instance.
     *
     * @param string $identifier The data file identifier
     * @param string $locale The preferred locale (if the data file is locale-specific)
     * @param string $fallbackLocale The fallback locale (if the data file is locale-specific)
     */
    public function __construct(string $identifier, string $locale = '', string $fallbackLocale = '')
    {
        $this->identifier = $identifier;

        if (empty($locale) && empty($fallbackLocale)) {
            $this->locale = '';
            $this->fallbackLocale = '';
            $message = "Unable to find the data file '$identifier'";
        } else {
            $this->locale = $locale;
            $this->fallbackLocale = $fallbackLocale;

            if (!strcasecmp($locale, $fallbackLocale)) {
                $message = "Unable to find the data file '$identifier' for '$locale'";
            } else {
                $message = "Unable to find the data file '$identifier', 
                neither for '$locale' nor for '$fallbackLocale'";
            }
        }
        parent::__construct($message);
    }

    /**
     * Retrieves the bad data file identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Retrieves the preferred locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Retrieves the fallback locale.
     *
     * @return string
     */
    public function getFallbackLocale(): string
    {
        return $this->fallbackLocale;
    }
}
