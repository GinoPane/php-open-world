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

    public function testAssertNotObject()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('OpenWorld\Assertions\Interfaces\AssertionInterface', TypeAssertion::CLASS_IMPLEMENTS_TYPE);

        $assertion->assertSingle(null);
    }

    public function testAssertImplements()
    {
        $assertion = new TypeAssertion('OpenWorld\Assertions\Interfaces\AssertionInterface', TypeAssertion::CLASS_IMPLEMENTS_TYPE);

        $assertion->assertSingle($assertion);
    }

    public function testAssertUses()
    {
        $assertion = new TypeAssertion('OpenWorld\Collections\Traits\ImplementsArray', TypeAssertion::CLASS_USES_TYPE);

        $assertion->assertSingle(new \OpenWorld\Collections\ArrayCollection());
    }

    public function testAssertNotImplements()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('AssertionInterface', TypeAssertion::CLASS_IMPLEMENTS_TYPE);

        $assertion->assertSingle($assertion);
    }

    public function testAssertNotUses()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('ImplementsArray', TypeAssertion::CLASS_USES_TYPE);

        $assertion->assertSingle(new \OpenWorld\Collections\ArrayCollection());
    }

    public function testAssertInherits()
    {
        $assertion = new TypeAssertion('OpenWorld\Collections\AbstractClasses\AssertionStrictCollection', TypeAssertion::CLASS_INHERITS_TYPE);

        $assertion->assertSingle(new \OpenWorld\Collections\DataProviderCollection(new TypeAssertion('integer')));
    }

    public function testAssertInheritsItself()
    {
        $assertion = new TypeAssertion(\OpenWorld\Collections\ArrayCollection::class, TypeAssertion::CLASS_INHERITS_TYPE);

        $assertion->assertSingle(new \OpenWorld\Collections\ArrayCollection());
    }

    public function testAssertNotInherits()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('ImplementsArray', TypeAssertion::CLASS_INHERITS_TYPE);

        $assertion->assertSingle(new \OpenWorld\Collections\DataProviderCollection(new TypeAssertion('integer')));
    }

    public function testClassDoesNotExistThrowsException()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('does not exist', TypeAssertion::CLASS_INHERITS_TYPE | TypeAssertion::CHECK_MODE_PARAMETERS);
    }

    public function testInterfaceDoesNotExistThrowsException()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('does not exist', TypeAssertion::CLASS_IMPLEMENTS_TYPE | TypeAssertion::CHECK_MODE_PARAMETERS);
    }

    public function testTraitDoesNotExistThrowsException()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('does not exist', TypeAssertion::CLASS_USES_TYPE | TypeAssertion::CHECK_MODE_PARAMETERS);
    }

    public function testClassDoesNotExistDoesNotThrowException()
    {
        $assertion = new TypeAssertion('does not exist', TypeAssertion::CLASS_INHERITS_TYPE);
    }

    public function testInterfaceDoesNotExistDoesNotThrowException()
    {
        $assertion = new TypeAssertion('does not exist', TypeAssertion::CLASS_IMPLEMENTS_TYPE);
    }

    public function testTraitDoesNotExistDoesNotThrowException()
    {
        $assertion = new TypeAssertion('does not exist', TypeAssertion::CLASS_USES_TYPE);
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
