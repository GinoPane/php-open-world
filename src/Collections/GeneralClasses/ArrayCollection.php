<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Collections\GeneralClasses;

use GinoPane\PhpOpenWorld\Collections\Traits\ImplementsArray;
use GinoPane\PhpOpenWorld\Collections\Interfaces\CollectionInterface;

/**
 * An ArrayCollection is a collection implementation that wraps a regular PHP array
 *
 * @package GinoPane\PhpOpenWorld\Collections
 */
class ArrayCollection implements CollectionInterface
{
    use ImplementsArray;

    /**
     * Initializes a new ArrayCollection.
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }
}
