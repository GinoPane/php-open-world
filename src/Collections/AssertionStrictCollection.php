<?php

namespace OpenWorld\Collections;

use Closure;
use OpenWorld\Assertions\Interfaces\AssertionInterface;
use OpenWorld\Collections\Interfaces\CollectionInterface;
use OpenWorld\Collections\Traits\ImplementsArray;

/**
 *
 * Class AssertionStrictCollection
 *
 * TypeStrictCollection use exception-based type checking to ensure, that
 * its elements has necessary type.
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

    public function set($key, $value)
    {
        $this->assertion->assertSingle($value);

        $this->elements[$key] = $value;
    }

    public function add($value) : CollectionInterface
    {
        $this->assertion->assertSingle($value);

        $this->elements[] = $value;

        return $this;
    }

    public function map(Closure $function) : CollectionInterface
    {
        return new static($this->assertion, array_map($function, $this->elements));
    }

    public function filter(Closure $predicate = null, int $flag = 0) : CollectionInterface
    {
        if ($predicate) {
            return new static($this->assertion, array_filter($this->elements, $predicate, $flag));
        } else {
            return new static($this->assertion, array_filter($this->elements));
        }
    }

    public function partition(Closure $predicate) : array
    {
        list($matches, $noMatches) = $this->splitIntoParts($predicate);

        return array(new static($this->assertion, $matches), new static($this->assertion, $noMatches));
    }
}
