<?php

use PHPUnit\Framework\TestCase;

use OpenWorld\Assertions\GeneralClasses\TypeAssertion;
use OpenWorld\Exceptions\InvalidTypeException;
use OpenWorld\Collections\GeneralClasses\AssertionStrictCollection;

/**
 *
 * Tests for
 *
 * @see OpenWorld\Collections\GeneralClasses\AssertionStrictCollection
 *
 */
class AssertionStrictCollectionTest extends TestCase
{
    /**
     * @test
     *
     * @param $item
     * @dataProvider provides_different_values
     */
    public function it_adds_items_to_a_collection($item)
    {
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'));
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
     * @dataProvider provides_different_key_values
     */
    public function it_sets_item_value_by_a_key($key, $value)
    {
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'));
        $collection->set($key, $value);

        $this->assertEquals(
            $collection->get($key), $value
        );
    }

    /**
     * @test
     *
     * @param $item
     * @dataProvider provides_wrong_different_values
     */
    public function it_throws_exception_for_wrong_values_on_add($item)
    {
        $this->expectException(InvalidTypeException::class);

        $collection = new AssertionStrictCollection(new TypeAssertion('integer'));
        $collection->add($item);
    }

    /**
     * @test
     *
     * @param $key
     * @param $value
     * @dataProvider provides_wrong_different_key_values
     */
    public function it_throws_exception_for_wrong_values_on_set($key, $value)
    {
        $this->expectException(InvalidTypeException::class);

        $collection = new AssertionStrictCollection(new TypeAssertion('integer'));
        $collection->set($key, $value);
    }

    /**
     * @test
     *
     * @param $elements
     * @dataProvider provides_different_elements
     */
    public function it_gets_collection_as_an_array($elements)
    {
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

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
    public function it_gets_the_first_collection_element($elements)
    {
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

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
    public function it_gets_the_last_collection_element($elements)
    {
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

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
    public function it_gets_all_collection_keys_as_an_array($elements)
    {
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

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
    public function it_gets_all_collection_values_as_an_array($elements)
    {
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

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
    public function it_counts_collection_elements($elements)
    {
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);
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
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);
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
     * @test
     */
    public function it_checks_elements_remove_by_key()
    {
        $elements = array(1, 'A' => 10, 2, 'null' => 7, 3, 'A2' => 5, 'zero' => 0);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);
        $this->assertEquals(1, $collection->removeKey(0));
        unset($elements[0]);
        $this->assertEquals(null, $collection->removeKey('non-existent'));
        unset($elements['non-existent']);
        $this->assertEquals(2, $collection->removeKey(1));
        unset($elements[1]);
        $this->assertEquals(10, $collection->removeKey('A'));
        unset($elements['A']);
        $this->assertEquals($elements, $collection->toArray());
    }

    /**
     * @test
     */
    public function it_checks_elements_remove_by_value()
    {
        $elements = array(1, 'A' => 10, 2, 'null' => 7, 3, 'A2' => 5, 'zero' => 0);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

        $this->assertTrue($collection->removeValue(1));
        unset($elements[0]);
        $this->assertFalse($collection->removeValue('non-existent'));
        $this->assertTrue($collection->removeValue(10));
        unset($elements['A']);
        $this->assertTrue($collection->removeValue(5));
        unset($elements['A2']);
        $this->assertEquals($elements, $collection->toArray());
    }

    /**
     * @test
     */
    public function it_checks_that_collection_contains_the_key()
    {
        $elements = array(1, 'A' => 10, 2, 'null' => 7, 3, 'A2' => 5, 'zero' => 0);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

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
    public function it_checks_an_empty_collection()
    {
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'));

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
    public function it_checks_if_collection_contains_a_value()
    {
        $elements = array(1, 'A' => 10, 2, 'null' => 7, 3, 'A2' => 5, 'zero' => 0);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

        $this->assertTrue(
            $collection->contains(0),
            "Contains zero"
        );

        $this->assertTrue(
            $collection->contains(10),
            "Contains 10"
        );

        $this->assertTrue(
            $collection->contains(2),
            "Contains 2"
        );

        $this->assertFalse(
            $collection->contains('non-existent'),
            "Doesn't contain an element"
        );
    }

    /**
     * @test
     */
    public function it_checks_if_collection_contains_an_item_by_callback()
    {
        $elements = array(1, 'A' => 10, 2, 'null' => 7, 3, 'A2' =>5, 'zero' => 0);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

        $this->assertTrue($collection->exists(function($key, $element) {
            return $key == 'A' && $element == 10;
        }), "Element exists");

        $this->assertFalse($collection->exists(function($key, $element) {
            return $key == 'non-existent' && $element == 'non-existent';
        }), "Element not exists");
    }

    /**
     * @test
     */
    public function it_check_the_index_of_method()
    {
        $elements = array(1, 'A' => 10, 2, 'null' => 7, 3, 'A2' => 5, 'zero' => 0);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

        $this->assertSame(
            array_search(2, $elements, true),
            $collection->indexOf(2),
            'Index of 2'
        );

        $this->assertSame(
            array_search(7, $elements, true),
            $collection->indexOf(7),
            'Index of 7'
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
    public function it_gets_elements_by_key()
    {
        $elements = array(1, 'A' => 10, 2, 'null' => 7, 3, 'A2' =>5, 'zero' => 0);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

        $this->assertSame(
            2,
            $collection->get(1),
            'Get element by index'
        );

        $this->assertSame(
            10,
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
    public function it_checks_offset_get_set_methods()
    {
        $elements = array(1, 'A' => 3, 2, 'null' => 4, 5, 'A2' => 6, 'zero' => 0, null => 7);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

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
    public function it_check_offset_exists()
    {
        $elements = array(1, 'A' => 3, 2, 'null' => 4, 5, 'A2' => 6, 'zero' => 0, null => 7);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

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
    public function it_gets_string_representation_of_a_collection()
    {
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'));

        $string = strval($collection);

        $this->assertTrue(gettype($string) == 'string');
    }

    /**
     * @test
     */
    public function it_filters_a_collection_against_a_callback()
    {
        $elements = array(0, 0, 0, 0);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

        $this->assertTrue(0 == $collection->filter()->count());

        $elements = array(1, 2, 3, 4);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

        $this->assertTrue($collection->filter(function($value) {
            return boolval($value % 2);
        })->count() == 2);

        $elements = array('0' => 1, false => 2, 3, 4);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

        $this->assertTrue($collection->filter(function($key) {
                return boolval($key);
        }, ARRAY_FILTER_USE_KEY)->count() == 2);

        $elements = array(1 => -1, 2 => 2, 3 => 0, 4 => -1);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

        $this->assertTrue($collection->filter(function($value, $key) {
                return ($key > 2 && $value < 0);
        }, ARRAY_FILTER_USE_BOTH)->count() == 1);
    }

    /**
     * @test
     */
    public function it_check_if_callback_is_valid_for_all_items()
    {
        $elements = array(0, 0, 0, 0);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

        $this->assertTrue($collection->forAll(function($key, $value) {
            return $value == false;
        }));

        $elements = array(1 => 1, 2 => 4, 3 => 9, 4 => 16, 5 => 24);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

        $this->assertFalse($collection->forAll(function($key, $value) {
            return $key ** 2 == $value;
        }));
    }

    /**
     * @test
     */
    public function it_splits_a_collection_into_partitions_by_callback()
    {
        $elements = array(1 => 1, 2 => 4, 3 => 9, 4 => 16, 5 => 24);
        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

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
    public function it_maps_collection_values()
    {
        $elements = array(1, 2, 3, 4, 5);
        $squares = array(1, 4, 9, 16, 25);

        $collection = new AssertionStrictCollection(new TypeAssertion('integer'), $elements);

        $this->assertEquals($squares, $collection->map(function($value){
            return $value ** 2;
        })->toArray());
    }

    /**
     * @return array
     */
    public function provides_different_elements()
    {
        return array(
            'indexed'     => array(array(1, 2, 3, 4, 5)),
            'associative' => array(array('A' => -1, 'B' => 2, 'C' => 3)),
            'mixed'       => array(array('A' => -5, 1, 'B' => 0, 2, 3)),
        );
    }

    /**
     * @return array
     */
    public function provides_different_values()
    {
        return [
            [2],
            [3],
            [-5]
        ];
    }

    /**
     * @return array
     */
    public function provides_different_key_values()
    {
        return [
            [3, -1],
            ['a', 0],
            [1, 1]
        ];
    }

    /**
     * @return array
     */
    public function provides_wrong_different_values()
    {
        return [
            ['a'],
            [null],
            [true]
        ];
    }

    /**
     * @return array
     */
    public function provides_wrong_different_key_values()
    {
        return [
            [3, 'a'],
            ['a', null],
            [1, true]
        ];
    }
}


