<?php

namespace OpenWorld\Collections;

use Closure;

/**
 *
 * Class DataProviderCollection
 *
 * DataProviderCollection use exception-based type checking to ensure, that
 * its elements has necessary type. Comments are also adjusted, so IDEs can use
 * them to generate proper code completion patterns.
 *
 * @package OpenWorld\Collections
 */
class DataProviderCollection extends ArrayCollection
{
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
    public function toArray() : array
    {
        return $this->elements;
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return isset($this->elements[$key]) ? $this->elements[$key] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getValues() : array
    {
        return array_values($this->elements);
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

    /**
     * {@inheritDoc}
     */
    public function partition(Closure $predicate) : array
    {
        $matches = $noMatches = array();

        foreach ($this->elements as $key => $element) {
            if ($predicate($key, $element)) {
                $matches[$key] = $element;
            } else {
                $noMatches[$key] = $element;
            }
        }

        return array(new static($matches), new static($noMatches));
    }
}
