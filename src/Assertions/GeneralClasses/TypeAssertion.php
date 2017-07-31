<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Assertions\GeneralClasses;

use OpenWorld\Exceptions\InvalidTypeException;
use OpenWorld\Assertions\Interfaces\AssertionInterface;

/**
 * Class TypeAssertion
 *
 * Makes sure that necessary type is being used.
 * Throws exception instead.
 *
 * @package OpenWorld\Assertions
 */
class TypeAssertion implements AssertionInterface
{
    /**
     * Specified type is a class, that must be extended or instantiated
     */
    const CLASS_INHERITS_TYPE = 1;

    /**
     * Specified type is an interface, that must be implemented
     */
    const CLASS_IMPLEMENTS_TYPE = 2;

    /**
     * Specified type is a trait, that must be used
     */
    const CLASS_USES_TYPE = 4;

    /**
     * Whether to allow non-existing/invalid classes/interfaces/traits to be used
     */
    const CHECK_MODE_PARAMETERS = 8;

    /**
     * Type to assert
     *
     * @var string
     */
    private $type = '';

    /**
     * Mode for classes behavior
     *
     * @var int
     */
    private $mode = 0;

    /**
     * TypeAssertion constructor.
     *
     * String parameter $type must specify full type name of the variable.
     * For classes use ::class or get_class().
     *
     * @see http://php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.class
     *
     * @param string $type
     * @param int $flag
     */
    public function __construct(string $type, int $flag = 0)
    {
        $this->type = $type;
        $this->mode = $flag ? $flag & ~self::CHECK_MODE_PARAMETERS : $flag;

        $performCheck = $flag & self::CHECK_MODE_PARAMETERS;

        if ($performCheck) {
            switch ($this->mode) {
                case self::CLASS_INHERITS_TYPE:
                    $this->assertClassExists($type);
                    break; // @codeCoverageIgnore
                case self::CLASS_IMPLEMENTS_TYPE:
                    $this->assertInterfaceExists($type);
                    break; // @codeCoverageIgnore
                case self::CLASS_USES_TYPE:
                    $this->assertTraitExists($type);
                    break; // @codeCoverageIgnore
            }
        }
    }

    /**
     * Assertion for a single item.
     *
     * @param $item
     * @throws InvalidTypeException
     */
    public function assertSingle($item)
    {
        $result = true;
        $message = '';
        $actualType = '';

        switch ($this->mode) {
            case self::CLASS_INHERITS_TYPE:
                $assertionResult = $this->handleClassInheritanceCheck($item);
                break;
            case self::CLASS_IMPLEMENTS_TYPE:
                $assertionResult = $this->handleInterfaceImplementationCheck($item);
                break;
            case self::CLASS_USES_TYPE:
                $assertionResult = $this->handleTraitUseCheck($item);
                break;
            default:
                $assertionResult = $this->handleSimpleCheck($item);
        }

        extract($assertionResult, EXTR_IF_EXISTS | EXTR_OVERWRITE);

        if (!$result) {
            throw new InvalidTypeException($actualType, $this->type, $message);
        }
    }

    /**
     * Assertion for multiple items.
     *
     * @param array $items
     * @return void
     */
    public function assertMultiple(array $items = []): void
    {
        foreach ($items as $item) {
            $this->assertSingle($item);
        }
    }

    /**
     * Perform a straightforward type check.
     *
     * @param $item
     * @return array
     */
    private function handleSimpleCheck($item): array
    {
        $result = true;

        $itemType = gettype($item);

        if (($this->type !== 'object') && ($itemType === 'object')) {
            $itemType = get_class($item);
        }

        if (strcasecmp($itemType, $this->type)) {
            $result = false;
        }

        return [
            'result' => $result,
            'actualType' => $itemType
        ];
    }

    /**
     * Check that an item inherits a class.
     *
     * @param $item
     * @return array
     */
    private function handleClassInheritanceCheck($item): array
    {
        $this->assertItemIsObject($item);

        $result = true;
        $message = '';
        $actualClasses = class_parents($item);

        if (get_class($item) !== $this->type && !isset($actualClasses[$this->type])) {
            $result = false;
            $message = "Variable's class must be or must extend '%2'. It extends only: %1";
        }

        return [
            'result' => $result,
            'actualType' => $actualClasses ? implode(", ", $actualClasses): 'nothing',
            'message' => $message
        ];
    }

    /**
     * Check that an item uses a trait.
     *
     * @param $item
     * @return array
     */
    private function handleTraitUseCheck($item): array
    {
        $this->assertItemIsObject($item);

        $result = true;
        $message = '';
        $actualTraits = class_uses($item);

        if (!isset($actualTraits[$this->type])) {
            $result = false;
            $message = "Variable's class must use '%2'. It uses only: %1";
        }

        return [
            'result' => $result,
            'actualType' => $actualTraits ? implode(", ", $actualTraits): 'nothing',
            'message' => $message
        ];
    }

    /**
     * Check that an item implements an interface.
     *
     * @param $item
     * @return array
     */
    private function handleInterfaceImplementationCheck($item): array
    {
        $this->assertItemIsObject($item);

        $result = true;
        $message = '';
        $actualInterfaces = class_implements($item);

        if (!isset($actualInterfaces[$this->type])) {
            $result = false;
            $message = "Variable's class must implement '%2'. It implements only: %1";
        }

        return [
            'result' => $result,
            'actualType' => $actualInterfaces ? implode(", ", $actualInterfaces): 'nothing',
            'message' => $message
        ];
    }

    /**
     * Assert that an item is an object.
     *
     * @param $item
     * @throws InvalidTypeException
     */
    private function assertItemIsObject($item)
    {
        $itemType = gettype($item);

        if (strcasecmp($itemType, 'object')) {
            throw new InvalidTypeException($itemType, 'object');
        }
    } // @codeCoverageIgnore

    /**
     * Assert that a trait exists.
     *
     * @param string $trait
     * @throws InvalidTypeException
     */
    private function assertTraitExists(string $trait)
    {
        if (!trait_exists($trait)) {
            throw new InvalidTypeException($trait, '', 'Specified trait does not exist: %1');
        }
    } // @codeCoverageIgnore

    /**
     * Assert that an interface exists.
     *
     * @param string $interface
     * @throws InvalidTypeException
     */
    private function assertInterfaceExists(string $interface)
    {
        if (!interface_exists($interface)) {
            throw new InvalidTypeException($interface, '', 'Specified interface does not exist: %1');
        }
    } // @codeCoverageIgnore

    /**
     * Assert that a class exists.
     *
     * @param string $class
     * @throws InvalidTypeException
     */
    private function assertClassExists(string $class)
    {
        if (!class_exists($class)) {
            throw new InvalidTypeException($class, '', 'Specified class does not exist: %1');
        }
    } // @codeCoverageIgnore
}
