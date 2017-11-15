<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Entities;

use GinoPane\PhpOpenWorld\Entities\AbstractClasses\CodeAssertedEntityAbstract;

/**
 * Class Language
 *
 * Represents ISO 639 language code
 *
 * @link https://en.wikipedia.org/wiki/ISO_639
 *
 * @package GinoPane\PhpOpenWorld\Entities
 */
class Language extends CodeAssertedEntityAbstract
{
    /**
     * 2/3-letter language code
     *
     * @var string
     */
    protected $code = '';

    /**
     * Source for language assertion data
     *
     * @var string
     */
    protected static $keySourceUri = 'language.codes.json';

    /**
     * Source for language aliases
     *
     * @var string
     */
    protected static $aliasSourceUri = 'language.alias.json';

    /**
     * Script constructor
     *
     * @param string $code Should be a valid ISO 639 2/3-letter language code. This code is being validated
     */
    public function __construct(string $code)
    {
        $this->assertCode(self::getCodeFromAlias($code));
    }

    /**
     * Get ISO 639 language code
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
        return boolval(preg_match('/^[a-z]{2,3}$/i', $code));
    }
}
