<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Collections\Interfaces;

use Closure;
use Countable;
use ArrayAccess;
use IteratorAggregate;

/**
 * Interface CollectionInterface
 * @package OpenWorld\Collections\Interfaces
 */
interface CollectionInterface extends Countable, IteratorAggregate, ArrayAccess
{
    /**
     * Adds an element at the end of the collection.
     *
     * @param mixed $element The element to add
     *
     * @return CollectionInterface
     */
    public function add($element): CollectionInterface;

    /**
     * Clears the collection, removing all elements.
     *
     * @return void
     */
    public function clear();

    /**
     * Checks whether an element is contained in the collection.
     * This is an O(n) operation, where n is the size of the collection.
     *
     * @param mixed $element The element to search for
     *
     * @return boolean TRUE if the collection contains the element, FALSE otherwise
     */
    public function contains($element): bool;

    /**
     * Checks whether the collection is empty (contains no elements).
     *
     * @return boolean TRUE if the collection is empty, FALSE otherwise
     */
    public function isEmpty(): bool;

    /**
     * Removes the element at the specified index from the collection.
     *
     * @param string|integer $key The kex/index of the element to remove
     *
     * @return mixed The removed element or NULL, if the collection did not contain the element
     */
    public function removeKey($key);

    /**
     * Removes the specified element from the collection, if it is found.
     *
     * @param mixed $element The element to remove
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeValue($element);

    /**
     * Checks whether the collection contains an element with the specified key/index.
     *
     * @param string|integer $key The key/index to check for
     *
     * @return boolean TRUE if the collection contains an element with the specified key/index,
     *                 FALSE otherwise
     */
    public function containsKey($key): bool;

    /**
     * Gets the element at the specified key/index.
     *
     * @param string|integer $key The key/index of the element to retrieve
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Gets all keys/indices of the collection.
     *
     * @return array The keys/indices of the collection, in the order of the corresponding
     *               elements in the collection
     */
    public function getKeys(): array;

    /**
     * Gets all values of the collection.
     *
     * @return array The values of all elements in the collection, in the order they
     *               appear in the collection
     */
    public function getValues(): array;

    /**
     * Sets an element in the collection at the specified key/index.
     *
     * @param string|integer $key The key/index of the element to set
     * @param mixed $value The element to set
     *
     * @return void
     */
    public function set($key, $value);

    /**
     * Gets a native PHP array representation of the collection.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Sets the internal iterator to the first element in the collection and returns this element.
     *
     * @return mixed
     */
    public function first();

    /**
     * Sets the internal iterator to the last element in the collection and returns this element.
     *
     * @return mixed
     */
    public function last();

    /**
     * Tests for the existence of an element that satisfies the given predicate.
     *
     * @param Closure $predicate The predicate
     *
     * @return boolean TRUE if the predicate is TRUE for at least one element, FALSE otherwise
     */
    public function exists(Closure $predicate): bool;

    /**
     * Returns all the elements of this collection that satisfy the predicate p.
     * The order of the elements is preserved.
     *
     * @param Closure $predicate The predicate used for filtering
     * @param int $flag ARRAY_FILTER_USE_KEY, ARRAY_FILTER_USE_BOTH
     *
     * @return CollectionInterface A collection with the results of the filter operation
     */
    public function filter(Closure $predicate = null, int $flag = 0): CollectionInterface;

    /**
     * Tests whether the given predicate p holds for all elements of this collection.
     *
     * @param Closure $predicate The predicate
     *
     * @return boolean TRUE, if the predicate yields TRUE for all elements, FALSE otherwise
     */
    public function forAll(Closure $predicate): bool;

    /**
     * Applies the given function to each element in the collection and returns
     * a new collection with the elements returned by the function.
     *
     * @param Closure $function
     *
     * @return CollectionInterface
     */
    public function map(Closure $function): CollectionInterface;

    /**
     * Partitions this collection in two collections according to a predicate.
     * Keys are preserved in the resulting collections.
     *
     * @param Closure $predicate The predicate on which to partition
     *
     * @return array An array with two elements. The first element contains the collection
     *               of elements where the predicate returned TRUE, the second element
     *               contains the collection of elements where the predicate returned FALSE
     */
    public function partition(Closure $predicate): array;

    /**
     * Gets the index/key of a given element. The comparison of two elements is strict,
     * that means not only the value but also the type must match.
     * For objects this means reference equality.
     *
     * @param mixed $element The element to search for
     *
     * @return int|string|bool The key/index of the element or FALSE if the element was not found
     */
    public function indexOf($element);
}
