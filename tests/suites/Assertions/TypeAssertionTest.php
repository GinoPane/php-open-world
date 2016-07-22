<?php

use PHPUnit\Framework\TestCase;
use OpenWorld\Assertions\TypeAssertion;
use OpenWorld\Exceptions\InvalidTypeException;

class TypeAssertionTest extends TestCase
{
    /**
     * @dataProvider provideVariablesWithValidTypes
     */
    public function testAssertionValid($variable, $expectedType)
    {
        $assertion = new TypeAssertion($expectedType);

        $assertion->assertSingle($variable);
    }

    /**
     * @dataProvider provideVariablesWithInvalidTypes
     */
    public function testAssertionInvalid($variable, $expectedType)
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion($expectedType);

        $assertion->assertSingle($variable);
    }

    public function testAssertMultiple()
    {
        $assertion = new TypeAssertion('integer');

        $assertion->assertMultiple([1, 0, -1]);
    }

    /**
     * @return array
     */
    public function provideVariablesWithValidTypes()
    {
        return [
            [3, 'integer'],
            [1.1, 'double'],
            ['string', 'string'],
            [null, 'null'],
            [true, 'boolean'],
            [new stdClass(), 'object'],
            [new TypeAssertion('integer'), 'OpenWorld\Assertions\TypeAssertion'],
        ];
    }

    /**
     * @return array
     */
    public function provideVariablesWithInvalidTypes()
    {
        return [
            [3, 'not integer'],
            [1.1, 'not double'],
            ['string', 'not string'],
            [null, 'not null'],
            [true, 'not boolean'],
            [new stdClass(), 'not object']
        ];
    }
}
