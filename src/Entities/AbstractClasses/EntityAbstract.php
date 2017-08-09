<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities\AbstractClasses;

use Closure;
use OpenWorld\OpenWorld;
use OpenWorld\Data\GeneralClasses\OpenWorldDataSource;
use OpenWorld\Exceptions\InvalidKeyPropertyValueException;

/**
 * Class EntityAbstract
 *
 * @package OpenWorld\Entities\AbstractClasses
 */
abstract class EntityAbstract
{

    /**
     * Key source URI which is a storage for assertion data
     *
     * @var string
     */
    protected $keySourceUri = '';

    /**
     * Asserts that the code value is valid (exists within the source)
     *
     * @param string $code
     * @param Closure $keyPredicate use Closure is your assert logic is more complicated than array check only
     *
     * @throws InvalidKeyPropertyValueException
     * @return void
     */
    protected abstract function assertCode(string $code, Closure $keyPredicate = null): void;

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
        $keySource = $this->getDataSourceLoader()->loadGeneral($this->keySourceUri)->asArray();

        if (
            (is_null($keyPredicate) && ($keyIndex = array_search(strtolower($code), array_map('strtolower', $keySource))) === false)
            ||
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
     * Returns entity code. It could be territory code for Territory, script code for Script
     *
     * @return string
     */
    public abstract function getCode(): string;

    /**
     * Returns OpenWorldDataSource instance to load source data
     *
     * @return OpenWorldDataSource
     */
    protected static function getDataSourceLoader(): OpenWorldDataSource
    {
        return OpenWorld::getDataSourceLoader();
    }
}