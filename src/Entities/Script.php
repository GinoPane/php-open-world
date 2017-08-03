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

        $this->assertCode($code);
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
    public function assertCode(string $code, Closure $keyPredicate = null): void
    {
        $this->code = $this->getAssertedCode($code, $keyPredicate);
    }
}