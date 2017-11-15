<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Entities\Interfaces;

use GinoPane\PhpOpenWorld\Entities\Locale;

/**
 * Interface Localizable
 *
 * Represents the interface for localizable entities
 *
 * @package GinoPane\PhpOpenWorld\Entities\Interfaces
 */
interface Localizable
{

    /**
     * @param Locale $locale
     *
     * @return mixed
     */
    public function in(Locale $locale);
}