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
     * @var string
     */
    protected $keySourceUri = 'script.codes.json';

    /**
     * Script constructor
     *
     * @param string $code Should be a valid ISO 15924 4-letter script code. This code is being validated
     */
    public function __construct(string $code)
    {
        $this->assertCode($code, $this->getDataSourceLoader());
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
     * @param OpenWorldDataSource $dataSource
     * @param Closure|null $keyPredicate
     *
     * @return void
     */
    public function assertCode(string $code, OpenWorldDataSource $dataSource, Closure $keyPredicate = null): void
    {
        $this->code = $this->getAssertedCode($code, $dataSource, $keyPredicate);
    }
}