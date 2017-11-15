<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Entities\Interfaces;

use GinoPane\PhpOpenWorld\Entities\Territory;

/**
 * Interface Territorial
 *
 * Represents the interface for territory-dependent entities
 *
 * @package GinoPane\PhpOpenWorld\Entities\Interfaces
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