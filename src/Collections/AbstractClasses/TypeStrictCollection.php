<?php

namespace OpenWorld\Collections\AbstractClasses;

use OpenWorld\Collections\ArrayCollection;
use OpenWorld\Collections\Interfaces\CollectionInterface;

/**
 *
 * Class TypeStrictCollection
 *
 * TypeStrictCollection use exception-based type checking to ensure, that
 * its elements has necessary type.
 *
 * @package OpenWorld\Collections
 */
abstract class TypeStrictCollection extends ArrayCollection
{
    /**
     * Type constraint for collection.
     *
     * @var string
     */
    protected $typeConstraint = '';

    /**
     * Initializes a new ArrayCollection.
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->elements[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function add($value) : CollectionInterface
    {
        $this->elements[] = $value;

        return $this;
    }

    protected function assertTypeMultiple(array $items)
    {

    }

    protected function assertTypeSingle()
    {

    }
}
