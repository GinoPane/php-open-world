<?php

use PHPUnit\Framework\TestCase;
use OpenWorld\Collections\GeneralClasses\ArrayCollection;

/**
 *
 * Tests for
 *
 * @see OpenWorld\Collections\GeneralClasses\ArrayCollection
 *
 */
class ArrayCollectionTest extends TestCase
{
    /**
     * @param $item
     *
     * @test
     * @dataProvider provides_different_values
     */
    public function it_adds_elements_to_the_collection($item)
    {
        $collection = new ArrayCollection();
        $collection->add($item);

        $this->assertTrue(
            false !== array_search($item, $collection->toArray())
        );
    }

    /**
     * @test
     *
     * @param $key
     * @param $value
     * @dataProvider provides_different_key_value_pairs
     */
    public function it_sets_elements_value_by_key($key, $value)
    {
        $collection = new ArrayCollection();
        $collection->set($key, $value);

        $this->assertEquals(
            $collection->get($key), $value
        );
    }

    /**
     * @test
     *
     * @param $elements
     * @dataProvider provides_different_elements
     */
    public function it_converts_collection_to_array(array $elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            $elements,
            $collection->toArray()
        );
    }

    /**
     * @test
     *
     * @param $elements
     * @dataProvider provides_different_elements
     */
    public function it_gets_the_first_collection_element(array $elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            reset($elements),
            $collection->first()
        );
    }

    /**
     * @test
     *
     * @param $elements
     * @dataProvider provides_different_elements
     */
    public function it_gets_the_last_collection_element(array $elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            end($elements),
            $collection->last()
        );
    }

    /**
     * @test
     *
     * @param $elements
     * @dataProvider provides_different_elements
     */
    public function it_gets_all_collection_keys_as_array($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            array_keys($elements),
            $collection->getKeys()
        );
    }

    /**
     * @test
     *
     * @param $elements
     * @dataProvider provides_different_elements
     */
    public function it_gets_all_collection_values_as_array($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(
            array_values($elements),
            $collection->getValues()
        );
    }

    /**
     * @test
     *
     * @param $elements
     * @dataProvider provides_different_elements
     */
    public function it_counts_collection_items($elements)
    {
        $collection = new ArrayCollection($elements);
        $this->assertSame(
            count($elements),
            $collection->count()
        );
    }

    /**
     * @test
     *
     * @param $elements
     * @dataProvider provides_different_elements
     */
    public function it_gets_collection_iterator($elements)
    {
//        $collection = new ArrayCollection($elements);
//        $iterations = 0;
//
//        foreach($collection->getIterator() as $key => $item) {
//            $this->assertSame(
//                $elements[$key],
//                $item,
//                "Item {$key} not match"
//            );
//
//            $iterations++;
//        }
//
//        $this->assertEquals(
//            count($elements),
//            $iterations,
//            "Number of iterations not match"
//        );
    }

    /**
     * @test
     *
     * @param $elements
     * @dataProvider provides_different_elements
     */
    public function it_runs_foreach_on_collection($elements)
    {
        $collection = new ArrayCollection($elements);
        $iterations = 0;

        foreach($collection as $key => $item) {
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
     * @test
     */
    public function it_removes_element_by_key()
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

    /**
     * @test
     */
    public function it_removes_element_by_value()
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

    /**
     * @test
     */
    public function it_checks_that_collection_contains_a_key()
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

    /**
     * @test
     */
    public function it_checks_empty_collections()
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

    /**
     * @test
     */
    public function it_checks_that_collection_contains_value()
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

    /**
     * @test
     */
    public function it_checks_that_element_exists_by_a_callback()
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

    /**
     * @test
     */
    public function it_checks_index_of_method()
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

    /**
     * @test
     */
    public function it_gets_elements_by_a_key()
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

    /**
     * @test
     */
    public function it_checks_offset_get_and_set()
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

    /**
     * @test
     */
    public function it_checks_offset_exists()
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

    /**
     * @test
     */
    public function it_gets_collections_as_a_string()
    {
        $collection = new ArrayCollection();

        $string = strval($collection);

        $this->assertTrue(gettype($string) == 'string');
    }

    /**
     * @test
     */
    public function it_filters_the_collection_by_a_condition()
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

    /**
     * @test
     */
    public function it_checks_condition_validity_for_all_elements()
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

    /**
     * @test
     */
    public function it_splits_collection_into_parts_by_a_condition()
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

    /**
     * @test
     */
    public function it_maps_collection_items()
    {
        $elements = array(1, 2, 3, 4, 5);
        $squares = array(1, 4, 9, 16, 25);

        $collection = new ArrayCollection($elements);

        $this->assertEquals($squares, $collection->map(function($value){
            return $value ** 2;
        })->toArray());
    }

    /**
     * @test
     * @dataProvider provides_different_elements
     * @param array $elements
     */
    public function it_checks_serialization_of_a_collection(array $elements)
    {
        $collection = new ArrayCollection($elements);
        
        $serialized = serialize($collection);

        $unserialized = unserialize($serialized);

        $this->assertEquals($collection, $unserialized);
    }

    /**
     * @return array
     */
    public function provides_different_elements()
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
    public function provides_different_values()
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
    public function provides_different_key_value_pairs()
    {
        return [
            [3, 'a'],
            ['a', null],
            [1, true]
        ];
    }
}


