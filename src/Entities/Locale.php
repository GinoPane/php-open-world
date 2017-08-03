<?php
/**
 * PHP Open World
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Entities;
use Closure;
use Exception;
use OpenWorld\Data\GeneralClasses\OpenWorldDataSource;
use OpenWorld\Entities\AbstractClasses\EntityAbstract;
use OpenWorld\Exceptions\InvalidKeyPropertyValueException;

/**
 * Class Locale
 *
 * @package OpenWorld\Entities
 */
class Locale extends EntityAbstract
{
    /**
     * Language entity
     *
     * @link https://en.wikipedia.org/wiki/ISO_639
     *
     * @var Language
     */
    private $language = null;

    /**
     * Script entity
     *
     * @link https://en.wikipedia.org/wiki/ISO_15924
     *
     * @var Script
     */
    private $script = null;

    /**
     * Territory entity
     *
     * @var Territory
     */
    private $territory = null;

    /**
     * Source for likely-subtags data
     *
     * @var string
     */
    private $likelySubtagsSourceUri = "likely.subtags.json";

    /**
     * Locale constructor
     *
     * @param Language $language
     * @param Script $script
     * @param Territory $territory
     */
    public function __construct(Language $language, Script $script = null, Territory $territory = null)
    {
        $this->language = $language;
        $this->script = $script;
        $this->territory = $territory;

        if (!$script || !$territory) {
            $this->fillMissingSubtags();
        }
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return Script
     */
    public function getScript(): ?Script
    {
        return $this->script;
    }

    /**
     * @return Territory
     */
    public function getTerritory(): ?Territory
    {
        return $this->territory;
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
    public function assertCode(string $code, Closure $keyPredicate = null): void
    {
        // TODO: Implement assertCode() method.
    }

    /**
     * Returns Locale code. The pattern is language_script_territory
     *
     * @return string
     */
    public function getCode(): string
    {
        // TODO: Implement getCode() method.
    }

    /**
     * Tries to fill missing subtags (script, territory) from likely-subtags data.
     * Missing subtags are not a big problem, so Exceptions would be converted to warnings
     *
     * @return void
     */
    protected function fillMissingSubtags(): void
    {
        try {
            $likelySubtags = $this->getDataSourceLoader()->loadGeneral($this->likelySubtagsSourceUri)->asArray();

            if (!empty($likelySubtags[$this->language->getCode()])) {
                $subtags = $likelySubtags[$this->language->getCode()];

                if (is_null($this->getScript())) {
                    $this->script = new Script($subtags['script']);
                }

                if (is_null($this->getTerritory())) {
                    $this->territory = new Territory($subtags['territory']);
                }
            }
        } catch (Exception $exception) {
            trigger_error("Likely subtags could not be loaded: {$exception->getMessage()}", E_USER_WARNING);
        }
    }
}