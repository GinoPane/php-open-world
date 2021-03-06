<?php

use PHPUnit\Framework\TestCase;

use OpenWorld\Assertions\GeneralClasses\TypeAssertion;
use OpenWorld\Exceptions\InvalidTypeException;
use OpenWorld\Collections\GeneralClasses\ArrayCollection;
use OpenWorld\Collections\GeneralClasses\AssertionStrictCollection;

/**
 * Class TypeAssertionTest
 */
class TypeAssertionTest extends TestCase
{
    /**
     * @test
     * @dataProvider provides_variables_with_valid_types
     */
    public function it_checks_valid_assertions($variable, $expectedType)
    {
        $assertion = new TypeAssertion($expectedType);

        $assertion->assertSingle($variable);
    }

    /**
     * @test
     * @dataProvider provides_variables_with_invalid_types
     */
    public function it_throws_exceptions_for_invalid_assertions($variable, $expectedType)
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion($expectedType);

        $assertion->assertSingle($variable);
    }

    /**
     * @test
     */
    public function it_check_multiple_valid_assertions()
    {
        $assertion = new TypeAssertion('integer');

        $assertion->assertMultiple([1, 0, -1]);
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_interface_assertion_with_invalid_object()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('OpenWorld\Assertions\Interfaces\AssertionInterface', TypeAssertion::CLASS_IMPLEMENTS_TYPE);

        $assertion->assertSingle(null);
    }

    /**
     * @test
     */
    public function it_checks_valid_interface_assertion()
    {
        $assertion = new TypeAssertion('OpenWorld\Assertions\Interfaces\AssertionInterface', TypeAssertion::CLASS_IMPLEMENTS_TYPE);

        $assertion->assertSingle($assertion);
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_interface_assertion_with_valid_object()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('AssertionInterface', TypeAssertion::CLASS_IMPLEMENTS_TYPE);

        $assertion->assertSingle($assertion);
    }

    /**
     * @test
     */
    public function it_checks_valid_trait_assertion()
    {
        $assertion = new TypeAssertion('OpenWorld\Collections\Traits\ImplementsArray', TypeAssertion::CLASS_USES_TYPE);

        $assertion->assertSingle(new ArrayCollection());
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_trait_assertion()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('ImplementsArray', TypeAssertion::CLASS_USES_TYPE);

        $assertion->assertSingle(new ArrayCollection());
    }

    /**
     * @test
     */
    public function it_checks_valid_self_inheritance_with_string_as_parameter()
    {
        $assertion = new TypeAssertion('OpenWorld\Collections\GeneralClasses\AssertionStrictCollection', TypeAssertion::CLASS_INHERITS_TYPE);

        $assertion->assertSingle(new AssertionStrictCollection(new TypeAssertion('integer')));
    }

    /**
     * @test
     */
    public function it_checks_valid_self_inheritance()
    {
        $assertion = new TypeAssertion(ArrayCollection::class, TypeAssertion::CLASS_INHERITS_TYPE);

        $assertion->assertSingle(new ArrayCollection());
    }

    /**
     * @test
     */
    public function it_throws_exceptions_for_invalid_inheritance()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('ImplementsArray', TypeAssertion::CLASS_INHERITS_TYPE);

        $assertion->assertSingle(new AssertionStrictCollection(new TypeAssertion('integer')));
    }

    /**
     * @test
     */
    public function it_throws_exception_for_non_existent_class_in_strict_mode()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('does not exist', TypeAssertion::CLASS_INHERITS_TYPE | TypeAssertion::CHECK_MODE_PARAMETERS);
    }

    /**
     * @test
     */
    public function it_throws_exception_for_non_existent_interface_in_strict_mode()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('does not exist', TypeAssertion::CLASS_IMPLEMENTS_TYPE | TypeAssertion::CHECK_MODE_PARAMETERS);
    }

    /**
     * @test
     */
    public function it_throws_exception_for_non_existent_trait_in_strict_mode()
    {
        $this->expectException(InvalidTypeException::class);

        $assertion = new TypeAssertion('does not exist', TypeAssertion::CLASS_USES_TYPE | TypeAssertion::CHECK_MODE_PARAMETERS);
    }

    /**
     * @test
     */
    public function it_does_not_throw_exception_for_non_existent_class_in_soft_mode()
    {
        $assertion = new TypeAssertion('does not exist', TypeAssertion::CLASS_INHERITS_TYPE);
    }

    /**
     * @test
     */
    public function it_does_not_throw_exception_for_non_existent_interface_in_soft_mode()
    {
        $assertion = new TypeAssertion('does not exist', TypeAssertion::CLASS_IMPLEMENTS_TYPE);
    }

    /**
     * @test
     */
    public function it_does_not_throw_exception_for_non_existent_trait_in_soft_mode()
    {
        $assertion = new TypeAssertion('does not exist', TypeAssertion::CLASS_USES_TYPE);
    }

    /**
     * @return array
     */
    public function provides_variables_with_valid_types()
    {
        return [
            [3, 'integer'],
            [1.1, 'double'],
            ['string', 'string'],
            [null, 'null'],
            [true, 'boolean'],
            [new stdClass(), 'object'],
            [new TypeAssertion('integer'), 'OpenWorld\Assertions\GeneralClasses\TypeAssertion'],
        ];
    }

    /**
     * @return array
     */
    public function provides_variables_with_invalid_types()
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
