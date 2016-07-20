<?php

namespace OpenWorld\Collections\Traits;

use ArrayIterator;
use Closure;
use OpenWorld\Collections\Interfaces\CollectionInterface;

trait ImplementsArray
{
    /**
     * An array containing the entries of this collection.
     *
     * @var array
     */
    protected $elements;

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
    public function first()
    {
        return reset($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        return end($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return next($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function removeKey($key)
    {
        if (!isset($this->elements[$key]) && !array_key_exists($key, $this->elements)) {
            return null;
        }

        $removed = $this->elements[$key];
        unset($this->elements[$key]);

        return $removed;
    }

    /**
     * {@inheritDoc}
     */
    public function removeValue($element)
    {
        $key = array_search($element, $this->elements, true);

        if ($key === false) {
            return false;
        }

        unset($this->elements[$key]);

        return true;
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($offset)) {
            $this->add($value);
        }

        $this->set($offset, $value);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        $this->removeKey($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key) : bool
    {
        return isset($this->elements[$key]) || array_key_exists($key, $this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element) : bool
    {
        return in_array($element, $this->elements, true);
    }

    /**
     * {@inheritDoc}
     */
    public function exists(Closure $predicate) : bool
    {
        foreach ($this->elements as $key => $element) {
            if ($predicate($key, $element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($element)
    {
        return array_search($element, $this->elements, true);
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
    public function getKeys() : array
    {
        return array_keys($this->elements);
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
    public function count() : int
    {
        return count($this->elements);
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
    public function isEmpty() : bool
    {
        return empty($this->elements);
    }

    /**
     * Required by interface IteratorAggregate.
     *
     * {@inheritDoc}
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function map(Closure $function) : CollectionInterface
    {
        return new static(array_map($function, $this->elements));
    }

    /**
     * {@inheritDoc}
     */
    public function filter(Closure $predicate) : CollectionInterface
    {
        return new static(array_filter($this->elements, $predicate));
    }

    /**
     * {@inheritDoc}
     */
    public function forAll(Closure $predicate) : bool
    {
        foreach ($this->elements as $key => $element) {
            if (!$predicate($key, $element)) {
                return false;
            }
        }

        return true;
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

    /**
     * Returns a string representation of this object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return __CLASS__ . '@' . spl_object_hash($this);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->elements = [];
    }
}