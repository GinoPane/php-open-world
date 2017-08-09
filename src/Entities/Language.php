<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities;

use Closure;
use OpenWorld\Data\GeneralClasses\OpenWorldDataSource;
use OpenWorld\Entities\AbstractClasses\EntityAbstract;
use OpenWorld\Entities\Traits\ImplementsAliasSubstitution;

/**
 * Class Language
 *
 * Represents ISO 639 language code
 *
 * @link https://en.wikipedia.org/wiki/ISO_639
 *
 * @package OpenWorld\Entities
 */
class Language extends EntityAbstract
{
    use ImplementsAliasSubstitution;

    /**
     * 2/3-letter language code
     *
     * @var string
     */
    protected $code = '';

    /**
     * Script constructor
     *
     * @param string $code Should be a valid ISO 639 2/3-letter language code. This code is being validated
     */
    public function __construct(string $code)
    {
        $this->keySourceUri = 'language.codes.json';
        $this->aliasSourceUri = 'language.alias.json';

        $this->assertCode($this->getCodeFromAlias($code, $this->getDataSourceLoader()));
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