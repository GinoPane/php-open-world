<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Collections\GeneralClasses;

use Closure;
use OpenWorld\Collections\Traits\ImplementsArray;
use OpenWorld\Assertions\Interfaces\AssertionInterface;
use OpenWorld\Collections\Interfaces\CollectionInterface;

/**
 * Class AssertionStrictCollection
 *
 * TypeStrictCollection uses exception-based type checking to ensure, that its elements has necessary type
 *
 * @package OpenWorld\Collections
 */
class AssertionStrictCollection implements CollectionInterface
{
    use ImplementsArray;

    /**
     * Type constraint assertion for collection.
     *
     * @var AssertionInterface
     */
    protected $assertion = null;

    /**
     * Initializes a new AssertionStrictCollection.
     *
     * @param AssertionInterface $assertion
     * @param array $elements
     */
    public function __construct(AssertionInterface $assertion, array $elements = [])
    {
        $this->assertion = $assertion;

        $this->assertion->assertMultiple($elements);

        $this->elements = $elements;
    }

    /**
     * Sets an element in the collection at the specified key/index.
     *
     * @param int|string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->assertion->assertSingle($value);

        $this->elements[$key] = $value;
    }

    /**
     * Adds an element at the end of the collection.
     *
     * @param mixed $value
     * @return CollectionInterface
     */
    public function add($value): CollectionInterface
    {
        $this->assertion->assertSingle($value);

        $this->elements[] = $value;

        return $this;
    }

    /**
     * Applies the given function to each element in the collection and returns
     * a new collection with the elements returned by the function.
     *
     * @param Closure $function
     * @return CollectionInterface
     */
    public function map(Closure $function): CollectionInterface
    {
        return new static($this->assertion, array_map($function, $this->elements));
    }

    /**
     * Returns all the elements of this collection that satisfy the predicate p.
     * The order of the elements is preserved.
     *
     * @param Closure|null $predicate
     * @param int $flag
     * @return CollectionInterface
     */
    public function filter(Closure $predicate = null, int $flag = 0): CollectionInterface
    {
        if ($predicate) {
            return new static($this->assertion, array_filter($this->elements, $predicate, $flag));
        } else {
            return new static($this->assertion, array_filter($this->elements));
        }
    }

    /**
     * Partitions this collection in two collections according to a predicate.
     * Keys are preserved in the resulting collections.
     *
     * @param Closure $predicate
     * @return array
     */
    public function partition(Closure $predicate): array
    {
        list($matches, $noMatches) = $this->splitIntoParts($predicate);

        return array(new static($this->assertion, $matches), new static($this->assertion, $noMatches));
    }
}
