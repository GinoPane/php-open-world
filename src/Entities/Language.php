<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities;

use Closure;
use OpenWorld\Entities\GeneralClasses\SingleCodeAssertedEntity;
use OpenWorld\Entities\Traits\ImplementsAliasSubstitution;
use OpenWorld\Entities\AbstractClasses\CodeAssertedEntityAbstract;

/**
 * Class Language
 *
 * Represents ISO 639 language code
 *
 * @link https://en.wikipedia.org/wiki/ISO_639
 *
 * @package OpenWorld\Entities
 */
class Language extends SingleCodeAssertedEntity
{
    use ImplementsAliasSubstitution;

    /**
     * 2/3-letter language code
     *
     * @var string
     */
    protected $code = '';

    protected static $keySourceUri = 'language.codes.json';

    /**
     * Script constructor
     *
     * @param string $code Should be a valid ISO 639 2/3-letter language code. This code is being validated
     */
    public function __construct(string $code)
    {
        self::$aliasSourceUri = 'language.alias.json';

        $this->assertCode(self::getCodeFromAlias($code, self::getDataSourceLoader()));
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
}
