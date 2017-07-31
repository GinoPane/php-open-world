<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities\Interfaces;

use OpenWorld\Entities\Territory;

/**
 * Interface Territorial
 *
 * Represents the interface for territory-dependent entities
 *
 * @package OpenWorld\Entities\Interfaces
 */
interface Territorial
{

    /**
     * @param Territory $territory
     *
     * @return mixed
     */
    public function from(Territory $territory);
}