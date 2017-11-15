<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers\Conditions;

use GinoPane\PhpOpenWorld\Entities\Locale;

/**
 * Class DataProviderCondition
 *
 * @package GinoPane\PhpOpenWorld\Data\GeneralClasses\Providers\Conditions
 */
class DataProviderCondition
{

    /**
     * Key for matching with provider
     *
     * @var string
     */
    private $key;

    /**
     * Additional data for provider
     *
     * @var Locale
     */
    private $locale;

    /**
     * DataProviderCondition constructor
     *
     * @param string $key
     * @param Locale $data
     */
    public function __construct(string $key, Locale $data = null)
    {
        $this->key = $key;
        $this->locale = $data;
    }

    /**
     * Get stored Locale
     *
     * @return null|Locale
     */
    public function getLocale(): ?Locale
    {
        return $this->locale;
    }

    /**
     * Get stored key
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Returns a readable representation of an instance
     *
     * @return string
     */
    public function __toString(): string
    {
        return "Key: {$this->getKey()}. "
            . (is_null($this->getLocale()) ? "" : "Locale: " . (string)($this->getLocale()));
    }
}
