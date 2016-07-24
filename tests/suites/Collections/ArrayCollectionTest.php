<?php

use PHPUnit\Framework\TestCase;
use OpenWorld\Collections\ArrayCollection;

/**
 *
 * Tests for {@see OpenWorld\Collections\ArrayCollection}
 *
 */
class ArrayCollectionTest extends TestCase
{
    /**
     * @param $item
     * @dataProvider provideDifferentValues
     */
    public function testAdd($item)
    {
        $collection = new ArrayCollection();
        $collection->add($item);

        $this->assertTrue(
            false !== array_search($item, $collection->toArray())
        );
    }

    /**
     * @param $key
     * @param $value
     * @dataProvider provideDifferentKeyValues
     */
    public function testSet($key, $value)
    {
        $collection = new ArrayCollection();
        $collection->set($key, $value);

        $this->assertEquals(
            $collection->get($key), $value
        );
    }

    /**
     * @param $elements
     * @dataProvider provideDifferentElements
     */
    public function testToArray($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            $elements,
            $collection->toArray()
        );
    }

    /**
     * @param $elements
     * @dataProvider provideDifferentElements
     */
    public function testFirst($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            reset($elements),
            $collection->first()
        );
    }

    /**
     * @param $elements
     * @dataProvider provideDifferentElements
     */
    public function testLast($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            end($elements),
            $collection->last()
        );
    }

    /**
     * @param $elements
     * @dataProvider provideDifferentElements
     */
    public function testKey($elements)
    {
        $collection = new ArrayCollection($elements);
        $this->assertSame(
            key($elements),
            $collection->key()
        );

        next($elements);

        $collection->next();
        $this->assertSame(
            key($elements),
            $collection->key()
        );
    }
    /**
     * @param $elements
     * @dataProvider provideDifferentElements
     */
    public function testNext($elements)
    {
        $collection = new ArrayCollection($elements);

        while (true) {
            $collectionNext = $collection->next();
            $arrayNext = next($elements);

            if(!$collectionNext || !$arrayNext) {
                break;
            }

            $this->assertSame(
                $arrayNext,
                $collectionNext,
                "Returned value of ArrayCollection::next() and next() not match"
            );

            $this->assertSame(
                key($elements),
                $collection->key(),
                "Keys not match"
            );

            $this->assertSame(
                current($elements),
                $collection->current(),
                "Current values not match"
            );
        }
    }

    /**
     * @param $elements
     * @dataProvider provideDifferentElements
     */
    public function testCurrent($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            current($elements),
            $collection->current()
        );

        next($elements);
        $collection->next();

        $this->assertSame(
            current($elements),
            $collection->current()
        );
    }

    /**
     * @param $elements
     * @dataProvider provideDifferentElements
     */
    public function testGetKeys($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            array_keys($elements),
            $collection->getKeys()
        );
    }

    /**
     * @param $elements
     * @dataProvider provideDifferentElements
     */
    public function testGetValues($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            array_values($elements),
            $collection->getValues()
        );
    }

    /**
     * @param $elements
     * @dataProvider provideDifferentElements
     */
    public function testCount($elements)
    {
        $collection = new ArrayCollection($elements);
        $this->assertSame(
            count($elements),
            $collection->count()
        );
    }

    /**
     * @param $elements
     * @dataProvider provideDifferentElements
     */
    public function testIterator($elements)
    {
        $collection = new ArrayCollection($elements);
        $iterations = 0;

        foreach($collection->getIterator() as $key => $item) {
            $this->assertSame(
                $elements[$key],
                $item,
                "Item {$key} not match"
            );

            $iterations++;
        }

        $this->assertEquals(
            count($elements),
            $iterations,
            "Number of iterations not match"
        );
    }

    /**
     * @return array
     */
    public function provideDifferentElements()
    {
        return array(
            'indexed'     => array(array(1, 2, 3, 4, 5)),
            'associative' => array(array('A' => 'a', 'B' => 'b', 'C' => 'c')),
            'mixed'       => array(array('A' => 'a', 1, 'B' => 'b', 2, 3)),
        );
    }

    /**
     * @return array
     */
    public function provideDifferentValues()
    {
        return [
            ['a'],
            [null],
            [1]
        ];
    }

    /**
     * @return array
     */
    public function provideDifferentKeyValues()
    {
        return [
            [3, 'a'],
            ['a', null],
            [1, true]
        ];
    }

    public function testRemove()
    {
        $elements = array(1, 'A' => 'a', 2, 'B' => 'b', 3);
        $collection = new ArrayCollection($elements);
        $this->assertEquals(1, $collection->removeKey(0));
        unset($elements[0]);
        $this->assertEquals(null, $collection->removeKey('non-existent'));
        unset($elements['non-existent']);
        $this->assertEquals(2, $collection->removeKey(1));
        unset($elements[1]);
        $this->assertEquals('a', $collection->removeKey('A'));
        unset($elements['A']);
        $this->assertEquals($elements, $collection->toArray());
    }

    public function testRemoveElement()
    {
        $elements = array(1, 'A' => 'a', 2, 'B' => 'b', 3, 'A2' => 'a', 'B2' => 'b');
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->removeValue(1));
        unset($elements[0]);
        $this->assertFalse($collection->removeValue('non-existent'));
        $this->assertTrue($collection->removeValue('a'));
        unset($elements['A']);
        $this->assertTrue($collection->removeValue('a'));
        unset($elements['A2']);
        $this->assertEquals($elements, $collection->toArray());
    }

    public function testContainsKey()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'B2' => 'b');
        $collection = new ArrayCollection($elements);

        $this->assertTrue(
            $collection->containsKey(0),
            "Contains index 0"
        );

        $this->assertTrue(
            $collection->containsKey('A'),
            "Contains key \"A\""
        );

        $this->assertTrue(
            $collection->containsKey('null'),
            "Contains key \"null\", with value null"
        );

        $this->assertFalse(
            $collection->containsKey('non-existent'),
            "Doesn't contain key"
        );
    }

    public function testEmpty()
    {
        $collection = new ArrayCollection();

        $this->assertTrue(
            $collection->isEmpty(),
            "Empty collection"
        );

        $collection->add(1);

        $this->assertFalse(
            $collection->isEmpty(),
            "Not empty collection"
        );
    }

    public function testContains()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0);
        $collection = new ArrayCollection($elements);

        $this->assertTrue(
            $collection->contains(0),
            "Contains Zero"
        );

        $this->assertTrue(
            $collection->contains('a'),
            "Contains \"a\""
        );

        $this->assertTrue(
            $collection->contains(null),
            "Contains Null"
        );

        $this->assertFalse(
            $collection->contains('non-existent'),
            "Doesn't contain an element"
        );
    }

    public function testExists()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0);
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->exists(function($key, $element) {
            return $key == 'A' && $element == 'a';
        }), "Element exists");

        $this->assertFalse($collection->exists(function($key, $element) {
            return $key == 'non-existent' && $element == 'non-existent';
        }), "Element not exists");
    }

    public function testIndexOf()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0);
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            array_search(2, $elements, true),
            $collection->indexOf(2),
            'Index of 2'
        );

        $this->assertSame(
            array_search(null, $elements, true),
            $collection->indexOf(null),
            'Index of null'
        );

        $this->assertSame(
            array_search('non-existent', $elements, true),
            $collection->indexOf('non-existent'),
            'Index of non existent'
        );
    }

    public function testGet()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0);
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            2,
            $collection->get(1),
            'Get element by index'
        );

        $this->assertSame(
            'a',
            $collection->get('A'),
            'Get element by name'
        );

        $this->assertSame(
            null,
            $collection->get('non-existent'),
            'Get non existent element'
        );
    }

    public function testOffsetSetGet()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0, null => 4);
        $collection = new ArrayCollection();

        foreach($elements as $key => $value) {
            $collection[$key] = $value;

            $this->assertSame(
                $value,
                $collection[$key],
                'Get element by key'
            );
        }

        $collection->clear();

        foreach($elements as $key => $value) {
            $collection[] = $value;

            $this->assertTrue(
                false !== array_search($value, $collection->toArray())
            );
        }
    }

    public function testOffsetUnsetExists()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0, null => 4);
        $collection = new ArrayCollection();

        foreach($elements as $key => $value) {
            $collection[$key] = $value;

            $this->assertTrue(
                isset($collection[$key]),
                'Get element by key'
            );

            unset($collection[$key]);

            $this->assertFalse(
                isset($collection[$key]),
                'Get element by key'
            );
        }
    }

    public function testToString()
    {
        $collection = new ArrayCollection();

        $string = strval($collection);

        $this->assertTrue(gettype($string) == 'string');
    }

    public function testFilter()
    {
        $elements = array(false, 0, null, '');
        $collection = new ArrayCollection($elements);

        $this->assertTrue(0 == $collection->filter()->count());

        $elements = array(1, 2, 3, 4);
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->filter(function($value) {
            return boolval($value % 2);
        })->count() == 2);

        $elements = array('0' => 1, false => 2, 3, 4);
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->filter(function($key) {
                return boolval($key);
        }, ARRAY_FILTER_USE_KEY)->count() == 2);

        $elements = array(1 => -1, 2 => 2, 3 => 0, 4 => -1);
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->filter(function($value, $key) {
                return ($key > 2 && $value < 0);
        }, ARRAY_FILTER_USE_BOTH)->count() == 1);
    }

    public function testForAll()
    {
        $elements = array(false, 0, null, '');
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->forAll(function($key, $value) {
            return $value == false;
        }));

        $elements = array(1 => 1, 2 => 4, 3 => 9, 4 => 16, 5 => 24);
        $collection = new ArrayCollection($elements);

        $this->assertFalse($collection->forAll(function($key, $value) {
            return $key ** 2 == $value;
        }));
    }

    public function testPartition()
    {
        $elements = array(1 => 1, 2 => 4, 3 => 9, 4 => 16, 5 => 24);
        $collection = new ArrayCollection($elements);

        list($part1, $part2) = $collection->partition(function($key, $value){
            return $key ** 2 == $value;
        });

        $this->assertTrue($part1->count() && $part1->count() && ($part1->count() + $part2->count() == $collection->count()),
            'Both returned'
        );

        list($part1, $part2) = $collection->partition(function($key, $value){
            return $key == 0;
        });

        $this->assertTrue(!$part1->count() && $part2->count() && ($part1->count() + $part2->count() == $collection->count()),
            'First empty'
        );

        list($part1, $part2) = $collection->partition(function($key, $value){
            return $key > 0;
        });

        $this->assertTrue($part1->count() && !$part2->count() && ($part1->count() + $part2->count() == $collection->count()),
            'Second empty'
        );
    }

    public function testMap()
    {
        $elements = array(1, 2, 3, 4, 5);
        $squares = array(1, 4, 9, 16, 25);

        $collection = new ArrayCollection($elements);

        $this->assertEquals($squares, $collection->map(function($value){
            return $value ** 2;
        })->toArray());
    }
}

