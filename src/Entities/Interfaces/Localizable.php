<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities\Interfaces;

use OpenWorld\Entities\Locale;

/**
 * Interface Localizable
 *
 * Represents the interface for localizable entities
 *
 * @package OpenWorld\Entities\Interfaces
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