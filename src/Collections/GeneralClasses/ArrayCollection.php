<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Collections\GeneralClasses;

use OpenWorld\Collections\Traits\ImplementsArray;
use OpenWorld\Collections\Interfaces\CollectionInterface;

/**
 * An ArrayCollection is a collection implementation that wraps a regular PHP array
 *
 * @package OpenWorld\Collections
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
