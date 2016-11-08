<?php

namespace OpenWorld\Collections\GeneralClasses;

use OpenWorld\Collections\Interfaces\CollectionInterface;
use OpenWorld\Collections\Traits\ImplementsArray;

/**
 * An ArrayCollection is a Collection implementation that wraps a regular PHP array.
 *
 * Warning: Using (un-)serialize() on a collection is not a supported use-case
 * and may break when we change the internals in the future. If you need to
 * serialize a collection use {@link toArray()} and reconstruct the collection
 * manually.
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
