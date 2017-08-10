<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities;

use Closure;
use OpenWorld\Entities\AbstractClasses\EntityAbstract;
use OpenWorld\Entities\Traits\ImplementsAliasSubstitution;

/**
 * Class Script
 *
 * Represents ISO 15924 4-letter language script code
 *
 * @link https://en.wikipedia.org/wiki/ISO_15924
 *
 * @package OpenWorld\Entities
 */
class Script extends EntityAbstract
{
    use ImplementsAliasSubstitution;

    /**
     * 4-letter script code
     *
     * @var string
     */
    protected $code = '';

    /**
     * Script constructor
     *
     * @param string $code Should be a valid ISO 15924 4-letter script code. This code is being validated
     */
    public function __construct(string $code)
    {
        $this->keySourceUri = 'script.codes.json';

        self::$aliasSourceUri = 'script.alias.json';

        $this->assertCode(self::getCodeFromAlias($code, self::getDataSourceLoader()));
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
     * Asserts that the code value is valid (exists within the source)
     *
     * @param string $code
     * @param Closure|null $keyPredicate
     *
     * @return void
     */
    protected function assertCode(string $code, Closure $keyPredicate = null): void
    {
        $this->code = $this->getAssertedCode($code, $keyPredicate);
    }
}