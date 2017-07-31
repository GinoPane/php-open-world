<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities\AbstractClasses;

use Closure;
use OpenWorld\Data\GeneralClasses\OpenWorldDataSource;
use OpenWorld\Data\GeneralClasses\Providers\GeneralProvider;
use OpenWorld\Data\GeneralClasses\Providers\LocaleProvider;
use OpenWorld\Data\GeneralClasses\SourceLoaderResults\Factories\JsonResultFactory;
use OpenWorld\Data\GeneralClasses\SourceLoaders\FileSourceLoader;
use OpenWorld\Exceptions\InvalidKeyPropertyValueException;

/**
 * Class EntityAbstract
 *
 * @package OpenWorld\Entities\AbstractClasses
 */
abstract class EntityAbstract
{

    /**
     * Source URI which is a storage for assertion data
     *
     * @var string
     */
    protected $keySourceUri = '';

    /**
     * Asserts that the code value is valid (exists within the source)
     *
     * @param string $code
     * @param OpenWorldDataSource $dataSource
     * @param Closure $keyPredicate use Closure is your assert logic is more complicated than array check only
     *
     * @throws InvalidKeyPropertyValueException
     * @return void
     */
    public abstract function assertCode(string $code, OpenWorldDataSource $dataSource, Closure $keyPredicate = null): void;

    /**
     * Asserts that the code value is valid (exists within the source)
     *
     * @param string $code
     * @param OpenWorldDataSource $dataSource
     * @param Closure $keyPredicate use Closure is your assert logic is more complicated than array check only
     *
     * @throws InvalidKeyPropertyValueException
     * @return mixed
     */
    public function getAssertedCode(string $code, OpenWorldDataSource $dataSource, Closure $keyPredicate = null)
    {
        $keySource = $dataSource->loadGeneral($this->keySourceUri)->asArray();

        if (
            (is_null($keyPredicate) && ($keyIndex = array_search(strtolower($code), array_map('strtolower', $keySource))) === false)
            ||
            (!is_null($keyPredicate) && !($validKeyValue = $keyPredicate($code, $keySource)))
        ) {
            throw new InvalidKeyPropertyValueException($code, __CLASS__);
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
    protected function getDataSourceLoader(): OpenWorldDataSource
    {
        return new OpenWorldDataSource(
            new GeneralProvider(
                new FileSourceLoader(),
                new JsonResultFactory()
            ),
            new LocaleProvider(
                new FileSourceLoader(),
                new JsonResultFactory()
            )
        );
    }
}