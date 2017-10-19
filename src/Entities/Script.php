<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities;

use OpenWorld\Entities\AbstractClasses\CodeAssertedEntityAbstract;

/**
 * Class Script
 *
 * Represents ISO 15924 4-letter language script code
 *
 * @link https://en.wikipedia.org/wiki/ISO_15924
 *
 * @package OpenWorld\Entities
 */
class Script extends CodeAssertedEntityAbstract
{
    /**
     * 4-letter script code
     *
     * @var string
     */
    protected $code = '';

    /**
     * Source for script assertion data
     *
     * @var string
     */
    protected static $keySourceUri = 'script.codes.json';

    /**
     * Source script aliases
     *
     * @var string
     */
    protected static $aliasSourceUri = 'script.alias.json';

    /**
     * Script constructor
     *
     * @param string $code Should be a valid ISO 15924 4-letter script code. This code is being validated
     */
    public function __construct(string $code)
    {
        $this->assertCode(self::getCodeFromAlias($code));
    }

    /**
     * Get ISO 15924 4-letter language script code
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
        return boolval(preg_match('/^[a-z]{4}$/i', $code));
    }
}
