<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities;

use Closure;
use OpenWorld\Entities\AbstractClasses\CodeAssertedEntityAbstract;
use OpenWorld\Entities\AbstractClasses\EntityAbstract;
use OpenWorld\Entities\GeneralClasses\SingleCodeAssertedEntity;
use OpenWorld\Entities\Traits\ImplementsAliasSubstitution;

/**
 * Class Variant
 *
 * Represents different variants or dialects of a single locale
 *
 * @package OpenWorld\Entities
 */
class Variant extends SingleCodeAssertedEntity
{
    /**
     * Variant code
     *
     * @var string
     */
    protected $code = '';

    protected static $keySourceUri = 'variant.codes.json';

    /**
     * Variant constructor
     *
     * @param string $code Should be a valid variant code. This code is being validated
     */
    public function __construct(string $code)
    {
        $this->assertCode($code);
    }

    /**
     * Get language variant code
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Validates the code with regular expression test
     *
     * @param string $code
     *
     * @return bool
     */
    public static function codeIsLikelyValid(string $code): bool
    {
        return boolval(preg_match('/^[0-9]{4}$|[0-9a-z]{5,}$/i', $code));
    }
}