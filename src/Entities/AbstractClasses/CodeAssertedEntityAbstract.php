<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities\AbstractClasses;

use Closure;
use Exception;
use OpenWorld\Exceptions\InvalidKeyPropertyValueException;

/**
 * Class CodeAssertedEntityAbstract
 *
 * @package OpenWorld\Entities\AbstractClasses
 */
abstract class CodeAssertedEntityAbstract extends EntityAbstract
{
    /**
     * Key source URI which is a storage for assertion data
     *
     * @var string
     */
    protected static $keySourceUri = '';

    /**
     * Asserts that the code value is valid (exists within the source)
     *
     * @param string $code
     * @param Closure $keyPredicate use Closure is your assert logic is more complicated than array check only
     *
     * @throws InvalidKeyPropertyValueException
     * @return void
     */
    abstract protected function assertCode(string $code, Closure $keyPredicate = null): void;

    /**
     * Asserts that the code value is valid (exists within the source)
     *
     * @param string $code
     * @param Closure $keyPredicate use Closure is your assert logic is more complicated than array check only
     *
     * @throws InvalidKeyPropertyValueException
     * @return mixed
     */
    public function getAssertedCode(string $code, Closure $keyPredicate = null)
    {
        $keySource = self::getDataSourceLoader()->loadGeneral(static::$keySourceUri);

        if ((is_null($keyPredicate) &&
            ($keyIndex = array_search(strtolower($code), array_map('strtolower', $keySource))) === false) ||
            (!is_null($keyPredicate) && !($validKeyValue = $keyPredicate($code, $keySource)))
        ) {
            throw new InvalidKeyPropertyValueException($code, get_called_class());
        } else {
            if (!empty($validKeyValue)) {
                return $validKeyValue;
            } else {
                return $keySource[$keyIndex];
            }
        }
    }

    /**
     * Tries to instantiate the instance and returns 'false' on error.
     * The return 'true' value means that code value is correct
     *
     * @param string $code
     *
     * @return bool
     */
    public static function checkIfCodeIsValid(string $code): bool
    {
        $codeIsValid = true;

        try {
            $entity = new static($code);
        } catch (Exception $exception) {
            $codeIsValid = false;
        } finally {
            unset($entity);
        }

        return $codeIsValid;
    }
}
