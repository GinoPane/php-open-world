<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities\GeneralClasses;

use Closure;
use Exception;
use OpenWorld\Entities\AbstractClasses\CodeAssertedEntityAbstract;
use OpenWorld\Exceptions\InvalidKeyPropertyValueException;

/**
 * Class SingleCodeAssertedEntity
 *
 * @package OpenWorld\Entities\AbstractClasses
 */
abstract class SingleCodeAssertedEntity extends CodeAssertedEntityAbstract
{
    /**
     * Entity code
     *
     * @var string
     */
    protected $code = '';

    /**
     * Get entity code
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Asserts that the code value is valid (exists within the source)
     *
     * @param string $code
     * @param Closure $keyPredicate use Closure is your assert logic is more complicated than array check only
     *
     * @throws InvalidKeyPropertyValueException
     * @return void
     */
    protected function assertCode(string $code, Closure $keyPredicate = null): void
    {
        $this->code = $this->getAssertedCode($code, $keyPredicate);
    }
}
