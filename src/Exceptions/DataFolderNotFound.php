<?php

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class DataFolderNotFound
 *
 * An exception raised when a data folder has not been found.
 *
 * @package OpenWorld\Exceptions
 */
class DataFolderNotFound extends ExceptionAbstract
{
    protected $locale;
    
    protected $fallbackLocale;

    /**
     * Initializes the instance.
     *
     * @param string $locale The preferred locale
     * @param string $fallbackLocale The fallback locale
     */
    public function __construct(string $locale, string $fallbackLocale)
    {
        $this->locale = $locale;
        $this->fallbackLocale = $fallbackLocale;

        if (!strcasecmp($locale, $fallbackLocale)) {
            $message = "Unable to find the specified locale folder for '$locale'";
        } else {
            $message = "Unable to find the specified locale folder, neither for '$locale' nor for '$fallbackLocale'";
        }

        parent::__construct($message);
    }

    /**
     * Retrieves the preferred locale.
     *
     * @return string
     */
    public function getLocale() : string
    {
        return $this->locale;
    }

    /**
     * Retrieves the fallback locale.
     *
     * @return string
     */
    public function getFallbackLocale() : string
    {
        return $this->fallbackLocale;
    }
}
