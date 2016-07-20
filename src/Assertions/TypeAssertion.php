<?php

namespace OpenWorld\Assertions;

use OpenWorld\Assertions\Interfaces\AssertionInterface;

/**
 * Class TypeAssertion.
 *
 * Makes sure that necessary type is being used.
 * Throws exception instead.
 *
 * @package OpenWorld\Assertions
 */
class TypeAssertion implements AssertionInterface
{

    private $type = '';

    /**
     * TypeAssertion constructor.
     *
     * String parameter $type must specify full type name of the variable.
     * For classes use ::class or get_class().
     *
     * @see http://php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.class
     *
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function assertSingle($item)
    {
        if ($item) {

        }
    }

    public function assertMultiple(array $items = [])
    {
        foreach($items as $item) {
            $this->assertSingle($item);
        }
    }
}