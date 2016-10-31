<?php

namespace OpenWorld\Assertions;

use OpenWorld\Assertions\Interfaces\AssertionInterface;
use OpenWorld\Exceptions\InvalidTypeException;

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
                    $this->ensureClassExists($type);
                    break; // @codeCoverageIgnore
                case self::CLASS_IMPLEMENTS_TYPE:
                    $this->ensureInterfaceExists($type);
                    break; // @codeCoverageIgnore
                case self::CLASS_USES_TYPE:
                    $this->ensureTraitExists($type);
                    break; // @codeCoverageIgnore
            }
        }
    }

    /**
     * @inheritdoc
     *
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
     * @inheritdoc
     *
     * @throws InvalidTypeException
     */
    public function assertMultiple(array $items = [])
    {
        foreach ($items as $item) {
            $this->assertSingle($item);
        }
    }

    private function handleSimpleCheck($item) : array
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

    private function handleClassInheritanceCheck($item)
    {
        $this->ensureItemIsObject($item);

        $result = true;
        $message = '';
        $actualClasses = class_parents($item);

        if (get_class($item) !== $this->type && !isset($actualClasses[$this->type])) {
            $result = false;
            $message = "Variable's class must be or must extend '%2'. It extends only: %1";
        }

        return [
            'result' => $result,
            'actualType' => $actualClasses ? implode(", ", $actualClasses) : 'nothing',
            'message' => $message
        ];
    }

    private function handleTraitUseCheck($item)
    {
        $this->ensureItemIsObject($item);

        $result = true;
        $message = '';
        $actualTraits = class_uses($item);

        if (!isset($actualTraits[$this->type])) {
            $result = false;
            $message = "Variable's class must use '%2'. It uses only: %1";
        }

        return [
            'result' => $result,
            'actualType' => $actualTraits ? implode(", ", $actualTraits) : 'nothing',
            'message' => $message
        ];
    }

    private function handleInterfaceImplementationCheck($item)
    {
        $this->ensureItemIsObject($item);

        $result = true;
        $message = '';
        $actualInterfaces = class_implements($item);

        if (!isset($actualInterfaces[$this->type])) {
            $result = false;
            $message = "Variable's class must implement '%2'. It implements only: %1";
        }

        return [
            'result' => $result,
            'actualType' => $actualInterfaces ? implode(", ", $actualInterfaces) : 'nothing',
            'message' => $message
        ];
    }

    private function ensureItemIsObject($item)
    {
        $itemType = gettype($item);

        if (strcasecmp($itemType, 'object')) {
            throw new InvalidTypeException($itemType, 'object');
        }
    }

    private function ensureTraitExists(string $trait)
    {
        if (!trait_exists($trait)) {
            throw new InvalidTypeException($trait, '', 'Specified trait does not exist: %1');
        }
    } // @codeCoverageIgnore

    private function ensureInterfaceExists(string $interface)
    {
        if (!interface_exists($interface)) {
            throw new InvalidTypeException($interface, '', 'Specified interface does not exist: %1');
        }
    } // @codeCoverageIgnore

    private function ensureClassExists(string $class)
    {
        if (!class_exists($class)) {
            throw new InvalidTypeException($class, '', 'Specified class does not exist: %1');
        }
    } // @codeCoverageIgnore
}
