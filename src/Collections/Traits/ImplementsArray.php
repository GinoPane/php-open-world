<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Collections\Traits;

use Closure;
use ArrayIterator;
use GinoPane\PhpOpenWorld\Collections\Interfaces\CollectionInterface;

/**
 * Class ImplementsArray
 *
 * @package GinoPane\PhpOpenWorld\Collections\Traits
 */
trait ImplementsArray
{
    /**
     * An array containing the entries of this collection
     *
     * @var array
     */
    protected $elements;

    /**
     * Gets a native PHP array representation of the collection
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * Sets the internal iterator to the first element in the collection and returns this element
     *
     * @return mixed
     */
    public function first()
    {
        return reset($this->elements);
    }

    /**
     * Sets the internal iterator to the last element in the collection and returns this element
     *
     * @return mixed
     */
    public function last()
    {
        return end($this->elements);
    }

    /**
     * Removes the element at the specified index from the collection
     *
     * @param string|integer $key The kex/index of the element to remove
     *
     * @return mixed The removed element or NULL, if the collection did not contain the element
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
     * Removes the specified element from the collection, if it is found
     *
     * @param mixed $element The element to remove
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeValue($element): bool
    {
        $key = array_search($element, $this->elements, true);

        if ($key === false) {
            return false;
        }

        unset($this->elements[$key]);

        return true;
    }

    /**
     * Required by interface ArrayAccess
     *
     * @param $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * Required by interface ArrayAccess
     *
     * @param $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Required by interface ArrayAccess
     *
     * @param $offset
     * @param $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Required by interface ArrayAccess
     *
     * @param $offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->removeKey($offset);
    }

    /**
     * Checks whether the collection contains an element with the specified key/index
     *
     * @param string|integer $key The key/index to check for
     *
     * @return boolean TRUE if the collection contains an element with the specified key/index,
     *                 FALSE otherwise
     */
    public function containsKey($key): bool
    {
        return isset($this->elements[$key]) || array_key_exists($key, $this->elements);
    }

    /**
     * Checks whether an element is contained in the collection.
     * This is an O(n) operation, where n is the size of the collection
     *
     * @param mixed $element The element to search for
     *
     * @return boolean TRUE if the collection contains the element, FALSE otherwise
     */
    public function contains($element): bool
    {
        return in_array($element, $this->elements, true);
    }

    /**
     * Tests for the existence of an element that satisfies the given predicate
     *
     * @param Closure $predicate The predicate
     *
     * @return boolean TRUE if the predicate is TRUE for at least one element, FALSE otherwise
     */
    public function exists(Closure $predicate): bool
    {
        foreach ($this->elements as $key => $element) {
            if ($predicate($key, $element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the index/key of a given element. The comparison of two elements is strict,
     * that means not only the value but also the type must match.
     * For objects this means reference equality
     *
     * @param mixed $element The element to search for
     *
     * @return int|string|bool The key/index of the element or FALSE if the element was not found
     */
    public function indexOf($element)
    {
        return array_search($element, $this->elements, true);
    }

    /**
     * Gets the element at the specified key/index.
     *
     * @param string|integer $key The key/index of the element to retrieve
     *
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->elements[$key]) ? $this->elements[$key] : null;
    }

    /**
     * Gets all keys/indices of the collection
     *
     * @return array The keys/indices of the collection, in the order of the corresponding
     *               elements in the collection
     */
    public function getKeys(): array
    {
        return array_keys($this->elements);
    }

    /**
     * Gets all values of the collection
     *
     * @return array The values of all elements in the collection, in the order they
     *               appear in the collection
     */
    public function getValues(): array
    {
        return array_values($this->elements);
    }

    /**
     * Returns the count of elements
     *
     * @return int Count of elements
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * Sets an element in the collection at the specified key/index
     *
     * @param string|integer $key The key/index of the element to set
     * @param mixed $value The element to set
     *
     * @return void
     */
    public function set($key, $value): void
    {
        $this->elements[$key] = $value;
    }

    /**
     * Adds an element at the end of the collection
     *
     * @param mixed $value The element to add
     *
     * @return CollectionInterface
     */
    public function add($value): CollectionInterface
    {
        $this->elements[] = $value;

        return $this;
    }

    /**
     * Checks whether the collection is empty (contains no elements)
     *
     * @return boolean TRUE if the collection is empty, FALSE otherwise
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * Required by interface IteratorAggregate
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * Applies the given function to each element in the collection and returns
     * a new collection with the elements returned by the function
     *
     * @param Closure $function
     *
     * @return CollectionInterface
     */
    public function map(Closure $function): CollectionInterface
    {
        return new static(array_map($function, $this->elements));
    }

    /**
     * Returns all the elements of this collection that satisfy the predicate.
     * The order of the elements is preserved
     *
     * @param Closure $predicate The predicate used for filtering
     * @param int $flag ARRAY_FILTER_USE_KEY, ARRAY_FILTER_USE_BOTH
     *
     * @return CollectionInterface A collection with the results of the filter operation
     */
    public function filter(Closure $predicate = null, int $flag = 0): CollectionInterface
    {
        if ($predicate) {
            return new static(array_filter($this->elements, $predicate, $flag));
        } else {
            return new static(array_filter($this->elements));
        }
    }

    /**
     * Tests whether the given predicate holds for all elements of this collection
     *
     * @param Closure $predicate The predicate
     *
     * @return boolean TRUE, if the predicate yields TRUE for all elements, FALSE otherwise
     */
    public function forAll(Closure $predicate): bool
    {
        foreach ($this->elements as $key => $element) {
            if (!$predicate($key, $element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Partitions this collection in two collections according to a predicate.
     * Keys are preserved in the resulting collections
     *
     * @param Closure $predicate The predicate on which to partition
     *
     * @return array An array with two elements. The first element contains the collection
     *               of elements where the predicate returned TRUE, the second element
     *               contains the collection of elements where the predicate returned FALSE
     */
    public function partition(Closure $predicate): array
    {
        list($matches, $noMatches) = $this->splitIntoParts($predicate);

        return array(new static($matches), new static($noMatches));
    }

    /**
     * Returns a string representation of this object
     *
     * @return string
     */
    public function __toString(): string
    {
        return __CLASS__ . '@' . spl_object_hash($this);
    }

    /**
     * Clears the collection, removing all elements
     *
     * @return void
     */
    public function clear(): void
    {
        $this->elements = [];
    }

    /**
     * Service function for partition
     *
     * @param Closure $predicate
     * @return array
     */
    protected function splitIntoParts(Closure $predicate): array
    {
        $matches = $noMatches = array();

        foreach ($this->elements as $key => $element) {
            if ($predicate($key, $element)) {
                $matches[$key] = $element;
            } else {
                $noMatches[$key] = $element;
            }
        }

        return [$matches, $noMatches];
    }
}
